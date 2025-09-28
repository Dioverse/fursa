<?php
namespace App\Notify;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Cache; // Added missing import for Cache
use Illuminate\Support\Facades\Log;
// Added missing import for Log

abstract class NotifyProcess
{
    public $gs;
    public $templateName;
    public $shortCodes = [];
    public $loopItems  = []; // New property for loop data
    public $user;
    public $template;
    public $message;
    public $createLog  = true;
    public $userColumn = 'user_id';
    public $subject;
    public $receiverName;
    public $toAddress;
    public $pushImage;
    public $email_from_name;

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
            $this->toAddress    = $this->user->email ?? $this->user->phone ?? null;
        }
    }

    public function __construct()
    {
        // Setting the configuration (gs) must be done in getMessage() after
        // prevConfiguration() runs, as $this->globalTemplate and $this->notifyConfig
        // are not yet set by the child class here.
    }

    /**
     * Main entry for preparing the notification message
     */
    protected function getMessage()
    {
        $this->resolveUser();
        $this->prevConfiguration();

        // 1. Fetch all necessary General Settings in one cached call
        $this->gs = gs([
            'site_name',
            'site_logo',
            $this->globalTemplate, // e.g., 'email_template'
            $this->notifyConfig,   // e.g., 'mail_config'
        ]);
        // Log::info("General Settings Fetched for Notification", $this->gs);

        $template = NotificationTemplate::where('act', $this->templateName)
            ->where($this->statusField, Status::ENABLE)
            ->first();
            
        $this->template = $template;
        
        if (! $template && $this->templateName) {
            return false;
        }
        $this->email_from_name = $template->email_sent_from_name;

        // Get subject first
        $this->getSubject();

        // Pick correct body field (email_body, sms_body, push_body, etc)
        $body = $template ? $template->{$this->body} : $this->message;

        // Process loop items first
        $body = $this->processLoopItems($body);

        // Wrap inside global template
        $message = $this->replaceShortCode(
            $this->gs[$this->globalTemplate],
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
     * Process loop items in the template
     * Supports syntax like:
     * {{#each products}}
     *   <tr>
     *     <td>{{name}}</td>
     *     <td>{{price}}</td>
     *     <td>{{quantity}}</td>
     *   </tr>
     * {{/each}}
     */
    protected function processLoopItems($content)
    {
        if (empty($this->loopItems)) {
            return $content;
        }

        // Match loop blocks with regex
        $pattern = '/\{\{#each\s+(\w+)\}\}(.*?)\{\{\/each\}\}/s';

        return preg_replace_callback($pattern, function ($matches) {
            $loopName     = $matches[1];
            $loopTemplate = $matches[2];

            if (! isset($this->loopItems[$loopName])) {
                return ''; // Remove the loop block if no data
            }

            $output = '';
            foreach ($this->loopItems[$loopName] as $item) {
                $itemOutput = $loopTemplate;

                // Replace placeholders in loop template
                foreach ($item as $key => $value) {
                    $itemOutput = str_replace('{{' . $key . '}}', $value, $itemOutput);
                }

                $output .= $itemOutput;
            }

            return $output;
        }, $content);
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
        $message = str_replace("{{logo}}", $this->gs['site_logo'], $message);
        $message = str_replace("{{app_name}}", $this->gs['site_name'], $message);
        $message = str_replace("{{year}}", date('Y'), $message);

        // User-related placeholders
        $message = str_replace("{{name}}", $this->receiverName ?? '', $message);
        $message = str_replace("{{toAddress}}", $this->toAddress ?? '', $message);

        return $message;
    }

    /**
     * Replace custom template shortcodes
     */
    protected function replaceTemplateShortCode($content): mixed
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
            'user_id'   => 0,
            'title'     => $message,
            'click_url' => '#',
        ]);
    }

    /**
     * Create notification log entry
     */
    public function createLog($type, $status = true, $errorMessage = null)
    {
        if ($this->user && $this->createLog) {
            $config = $this->gs[$this->notifyConfig];

            NotificationLog::create([
                $this->userColumn   => $this->user->id ?? null,
                'notification_type' => $type,
                'sender'            => $config['name'] ?? 'system',
                'sent_from'         => $this->sentFrom,
                'sent_to'           => $type === 'push' ? 'Firebase Token' : $this->toAddress,
                'subject'           => $this->subject,
                'image'             => $this->pushImage ?? null,
                'message'           => $type === 'email' ? $this->finalMessage : strip_tags($this->finalMessage),
                'status'            => $status,
                'error_message'     => $errorMessage,
            ]);
        }
    }
}
