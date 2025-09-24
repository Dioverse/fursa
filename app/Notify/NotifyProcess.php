<?php

namespace App\Notify;

use App\Models\User;
use App\Models\NotificationTemplate;
use App\Models\NotificationLog;
use App\Models\AdminNotification;
use App\Constants\Status;

abstract class NotifyProcess
{
    public $templateName;
    public $shortCodes = [];
    public $user;
    public $template;
    public $message;
    public $createLog = true;
    public $userColumn = 'user_id';
    public $subject;
    public $receiverName;
    public $toAddress;
    public $pushImage;

    protected $statusField;
    protected $globalTemplate;
    protected $body;
    protected $notifyConfig;
    protected $finalMessage;
    protected $sentFrom = null;

    abstract public function send();
    abstract protected function prevConfiguration();

    /**
     * If $this->user is numeric, resolve to actual User instance
     */
    protected function resolveUser()
    {
        if (is_numeric($this->user)) {
            $this->user = User::find($this->user);
        }

        if ($this->user) {
            $this->receiverName = $this->user->name ?? $this->user->first_name ?? 'User';
            $this->toAddress = $this->user->email ?? $this->user->phone ?? null;
        }
    }

    /**
     * Main entry for preparing the notification message
     */
    protected function getMessage()
    {
        $this->resolveUser();
        $this->prevConfiguration();

        $template = NotificationTemplate::where('act', $this->templateName)
            ->where($this->statusField, Status::ENABLE)
            ->first();

        $this->template = $template;

        if (!$template && $this->templateName) {
            return false;
        }

        // Get subject first
        $this->getSubject();

        // Pick correct body field (email_body, sms_body, push_body, etc)
        $body = $template ? $template->{$this->body} : $this->message;

        // Wrap inside global template
        $message = $this->replaceShortCode(
            gs($this->globalTemplate),
            $body
        );

        // Replace template shortcodes like {{user_name}}, {{test_message}}, etc.
        if ($this->shortCodes) {
            $message = $this->replaceTemplateShortCode($message);
        }

        $this->finalMessage = $message;
        return $message;
    }

    /**
     * Replace placeholders in the global template
     */
    protected function replaceShortCode($template, $body)
    {
        $message = $template;

        // Global placeholders
        $message = str_replace("{{body}}", $body, $message);
        $message = str_replace("{{subject}}", $this->subject ?? '', $message);
        $message = str_replace("{{logo}}", asset('images/logo.png'), $message);
        $message = str_replace("{{app_name}}", config('app.name'), $message);
        $message = str_replace("{{year}}", date('Y'), $message);

        // User-related placeholders
        $message = str_replace("{{name}}", $this->receiverName ?? '', $message);
        $message = str_replace("{{toAddress}}", $this->toAddress ?? '', $message);

        return $message;
    }

    /**
     * Replace custom template shortcodes
     */
    protected function replaceTemplateShortCode($content)
    {
        foreach ($this->shortCodes as $code => $value) {
            $content = str_replace('{{' . $code . '}}', $value, $content);
        }
        return $content;
    }

    /**
     * Build subject with shortcodes replaced
     */
    protected function getSubject()
    {
        if ($this->template) {
            $subject = $this->template->subject ?? '';

            if ($this->shortCodes) {
                foreach ($this->shortCodes as $code => $value) {
                    $subject = str_replace('{{' . $code . '}}', $value, $subject);
                }
            }

            $this->subject = $subject;
        }
    }

    /**
     * Create admin error log
     */
    public function createErrorLog($message)
    {
        AdminNotification::create([
            'user_id' => 0,
            'title' => $message,
            'click_url' => '#'
        ]);
    }

    /**
     * Create notification log entry
     */
    public function createLog($type, $status = true, $errorMessage = null)
    {
        if ($this->user && $this->createLog) {
            $config = gs($this->notifyConfig);

            NotificationLog::create([
                $this->userColumn => $this->user->id ?? null,
                'notification_type' => $type,
                'sender' => $config['name'] ?? 'system',
                'sent_from' => $this->sentFrom,
                'sent_to' => $type === 'push' ? 'Firebase Token' : $this->toAddress,
                'subject' => $this->subject,
                'image' => $this->pushImage ?? null,
                'message' => $type === 'email' ? $this->finalMessage : strip_tags($this->finalMessage),
                'status' => $status,
                'error_message' => $errorMessage
            ]);
        }
    }
}
