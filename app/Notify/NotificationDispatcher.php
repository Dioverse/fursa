<?php
namespace App\Notify;

use App\Models\AdminNotification;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessNotificationJob;
use App\Jobs\ProcessBulkNotificationJob;

class NotificationDispatcher
{
    public $templateName;
    public $shortCodes;
    public $sendVia;
    public $user;
    public $createLog;
    public $userColumn;
    public $pushImage;
    public $message;
    public $toAddress;
    public $receiverName;

    public function __construct($sendVia = null)
    {
        $this->sendVia = $sendVia;
    }

    public function send()
    {
        $methods = $this->getAvailableMethods();
        
        foreach ($methods as $methodName => $methodClass) {
            try {
                $notify = new $methodClass;
                $this->configureNotifier($notify);
                $notify->send();
            } catch (\Exception $e) {
                $this->handleError($methodName, $e);
            }
        }
    }

    public function sendBulk(array $notifications, string $method)
    {
        $methodClass = $this->notifyMethods()[$method] ?? null;
        
        if (!$methodClass) {
            throw new \InvalidArgumentException("Invalid notification method: {$method}");
        }

        $notify = new $methodClass;
        $notify->sendBulk($notifications);
    }

    private function getAvailableMethods()
    {
        $methods = [];
        $available = $this->notifyMethods();

        if ($this->sendVia) {
            foreach ($this->sendVia as $sendVia) {
                if (!isset($available[$sendVia])) continue;
                if (!$this->isMethodEnabled($sendVia)) continue;
                $methods[$sendVia] = $available[$sendVia];
            }
        } else {
            foreach ($available as $key => $class) {
                if (!$this->isMethodEnabled($key)) continue;
                $methods[$key] = $class;
            }
        }

        return $methods;
    }

    private function isMethodEnabled($method)
    {
        switch ($method) {
            case 'email': return gs('en');
            case 'push': return gs('pn');
            case 'sms': return gs('sn');
            default: return true;
        }
    }

    private function configureNotifier($notify)
    {
        $notify->templateName = $this->templateName;
        $notify->shortCodes = $this->shortCodes;
        $notify->user = $this->user;
        $notify->createLog = $this->createLog;
        $notify->userColumn = $this->userColumn;
        $notify->pushImage = $this->pushImage ?? null;
        $notify->message = $this->message;
        $notify->toAddress = $this->toAddress;
        $notify->receiverName = $this->receiverName;
    }

    private function handleError($method, \Exception $e)
    {
        Log::error("Notification {$method} failed", [
            'template' => $this->templateName,
            'user' => $this->user->id ?? null,
            'error' => $e->getMessage()
        ]);

        AdminNotification::create([
            'user_id' => 0,
            'title' => "Notification {$method} failed: " . $e->getMessage(),
            'click_url' => '#'
        ]);
    }

    protected function notifyMethods()
    {
        return [
            'email' => Email::class,
            'sms' => Sms::class,
            'push' => Push::class,
        ];
    }
}