<?php
namespace App\Notify;

use SendGrid;
use SendGrid\Mail\To;
use SendGrid\Mail\From;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Log;
use Mailjet\Client as MailjetClient;
use SendGrid\Mail\Mail as SendGridMail;
use Mailjet\Resources as MailjetResources;

class Email extends NotifyProcess
{
    public $metGS;
    public $email_from_name;

    protected function prevConfiguration()
    {
        $this->metGS = gs(['email_from','mail_config','email_from_name']);
        $this->body = 'email_body';
        $this->statusField = 'email_status';
        $this->globalTemplate = 'email_template';
        $this->notifyConfig = 'mail_config';
        $this->sentFrom = $this->metGS['email_from'];
        $this->toAddress = $this->user->email ?? $this->toAddress;
        $this->receiverName = $this->user->name ?? $this->user->first_name ?? $this->receiverName;
    }

    public function send()
    {
        $message = $this->getMessage();
        if (!$message) {
            return false;
        }

        $config = $this->metGS['mail_config'];
        
        try {
            switch ($config['name']) {
                case 'mailjet':
                    return $this->sendViaMailjet();
                case 'sendgrid':
                    return $this->sendViaSendGrid();
                case 'smtp':
                    return $this->sendViaSMTP   ();
                default:
                    return $this->sendViaPhpMail();
            }
        } catch (\Exception $e) {
            $this->createErrorLog('Email sending failed: ' . $e->getMessage());
            $this->createLog('email', false, $e->getMessage());
            return false;
        }
    }

    public function sendBulk(array $notifications)
    {
        $config = $this->metGS['mail_config'];
        
        switch ($config['name']) {
            case 'mailjet':
                return $this->sendBulkViaMailjet($notifications);
            case 'sendgrid':
                return $this->sendBulkViaSendGrid($notifications);
            default:
                // Fallback to individual sends for non-bulk services
                foreach ($notifications as $notification) {
                    $this->configureFromArray($notification);
                    $this->send();
                }
        }
    }

    private function sendViaPhpMail()
    {
        $headers = [
            'From' => ($this->email_from_name ?? $this->metGS['email_from_name']) . ' <' . $this->metGS['email_from'] . '>',
            'Reply-To' => $this->metGS['email_from'],
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Mailer' => 'PHP/' . phpversion()
        ];

        $success = mail(
            $this->toAddress,
            $this->subject,
            $this->finalMessage,
            implode("\r\n", array_map(fn($k, $v) => "$k: $v", array_keys($headers), $headers))
        );

        $this->createLog('email', $success);
        return $success;
    }

    private function sendViaSMTP()
    {
        $config = $this->metGS['mail_config'];
        
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['enc'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['port'];
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($this->metGS['email_from'], ($this->email_from_name ?? $this->metGS['email_from_name']));
        $mail->addAddress($this->toAddress, $this->receiverName ?? '');
        
        $mail->isHTML(true);
        $mail->Subject = $this->subject;
        $mail->Body = $this->finalMessage;

        $success = $mail->send();
        $this->createLog('email', $success);
        
        return $success;
    }

    private function sendViaMailjet()
    {
        $config = $this->metGS['mail_config'];
        
        $mj = new MailjetClient($config['api_key'], $config['api_secret'], true, ['version' => 'v3.1']);
        
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->metGS['email_from'],
                        'Name' => ($this->email_from_name ?? $this->metGS['email_from_name'])
                    ],
                    'To' => [
                        [
                            'Email' => $this->toAddress,
                            'Name' => $this->receiverName ?? ''
                        ]
                    ],
                    'Subject' => $this->subject,
                    'HTMLPart' => $this->finalMessage,
                ]
            ]
        ];

        $response = $mj->post(MailjetResources::$Email, ['body' => $body]);
        $success = $response->success();
        
        $this->createLog('email', $success, $success ? null : $response->getReasonPhrase());
        return $success;
    }

    private function sendViaSendGrid()
    {
        $config = $this->metGS['mail_config'];
        
        $email = new SendGridMail();
        $email->setFrom($this->metGS['email_from'], ($this->email_from_name ?? $this->metGS['email_from_name']));
        $email->setSubject($this->subject);
        $email->addTo($this->toAddress, $this->receiverName ?? '');
        $email->addContent("text/html", $this->finalMessage);

        $sendgrid = new SendGrid($config['api_key']);
        
        try {
            $response = $sendgrid->send($email);
            $success = $response->statusCode() >= 200 && $response->statusCode() < 300;
            
            $this->createLog('email', $success, $success ? null : $response->body());
            return $success;
        } catch (\Exception $e) {
            $this->createLog('email', false, $e->getMessage());
            return false;
        }
    }

    private function sendBulkViaMailjet(array $notifications)
    {
        $config = $this->metGS['mail_config'];
        $mj = new MailjetClient($config['api_key'], $config['api_secret'], true, ['version' => 'v3.1']);
        
        $messages = [];
        foreach ($notifications as $notification) {
            $messages[] = [
                'From' => [
                    'Email' => $this->metGS['email_from'],
                    'Name' => ($this->email_from_name ?? $this->metGS['email_from_name'])
                ],
                'To' => [
                    [
                        'Email' => $notification['toAddress'],
                        'Name' => $notification['receiverName'] ?? ''
                    ]
                ],
                'Subject' => $notification['subject'],
                'HTMLPart' => $notification['message'],
            ];
        }

        $body = ['Messages' => $messages];
        $response = $mj->post(MailjetResources::$Email, ['body' => $body]);
        
        return $response->success();
    }

    private function sendBulkViaSendGrid(array $notifications)
    {
        $config = $this->metGS['mail_config'];
        $sendgrid = new SendGrid($config['api_key']);
        
        foreach (array_chunk($notifications, 1000) as $chunk) {
            $emails = [];
            
            foreach ($chunk as $notification) {
                $email = new SendGridMail();
                $email->setFrom($this->metGS['email_from'], ($this->email_from_name ?? $this->metGS['email_from_name']));
                $email->setSubject($notification['subject']);
                $email->addTo($notification['toAddress'], $notification['receiverName'] ?? '');
                $email->addContent("text/html", $notification['message']);
                $emails[] = $email;
            }
            
            foreach ($emails as $email) {
                $sendgrid->send($email);
            }
        }
        
        return true;
    }

    private function configureFromArray(array $data)
    {
        $this->templateName = $data['templateName'];
        $this->shortCodes = $data['shortCodes'] ?? [];
        $this->user = $data['user'] ?? null;
        $this->toAddress = $data['toAddress'];
        $this->receiverName = $data['receiverName'] ?? null;
        $this->message = $data['message'] ?? null;
    }
}
