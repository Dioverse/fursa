<?php
namespace App\Notify;

use Twilio\Rest\Client as TwilioClient;
use Vonage\Client as VonageClient;
use Vonage\Client\Credentials\Basic as VonageCredentials;
use Vonage\SMS\Message\SMS as VSMS;

class Sms extends NotifyProcess
{
    protected function prevConfiguration()
    {
        $this->body = 'sms_body';
        $this->statusField = 'sms_status';
        $this->globalTemplate = 'sms_template';
        $this->notifyConfig = 'sms_config';
        $this->sentFrom = gs('sms_from');
        $this->toAddress = $this->user->phone ?? $this->toAddress;
    }

    public function send()
    {
        $message = $this->getMessage();
        
        if (!$message) {
            return false;
        }

        $config = gs('sms_config');
        
        try {
            switch ($config['name']) {
                case 'twilio':
                    return $this->sendViaTwilio();
                case 'nexmo':
                case 'vonage':
                    return $this->sendViaVonage();
                default:
                    return false;
            }
        } catch (\Exception $e) {
            $this->createErrorLog('SMS sending failed: ' . $e->getMessage());
            $this->createLog('sms', false, $e->getMessage());
            return false;
        }
    }

    public function sendBulk(array $notifications)
    {
        foreach ($notifications as $notification) {
            $this->configureFromArray($notification);
            $this->send();
        }
    }

    private function sendViaTwilio()
    {
        $config = gs('sms_config');
        $twilio = $config['twilio'];
        
        $client = new TwilioClient($twilio['account_sid'], $twilio['auth_token']);
        
        $message = $client->messages->create(
            $this->toAddress,
            [
                'from' => $twilio['from'],
                'body' => strip_tags($this->finalMessage)
            ]
        );

        $success = $message->sid !== null;
        $this->createLog('sms', $success);
        
        return $success;
    }

    private function sendViaVonage()
    {
        $config = gs('sms_config');
        
        $basic = new VonageCredentials($config['api_key'], $config['api_secret']);
        $client = new VonageClient($basic);
        
        $message = new VSMS($this->toAddress, $config['from'], strip_tags($this->finalMessage));
        $response = $client->sms()->send($message);
        
        $success = $response->current()->getStatus() == 0;
        $this->createLog('sms', $success);
        
        return $success;
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