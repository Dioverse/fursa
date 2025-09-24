<?php
namespace App\Jobs;

use App\Models\AdminNotification;
use Illuminate\Support\Facades\Log;
use App\Notify\NotificationDispatcher;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
// use Illuminate\Bus\Queueable;

class ProcessNotificationJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 120;
    public $maxExceptions = 1;

    protected $notificationData;

    public function __construct(array $notificationData)
    {
        $this->notificationData = $notificationData;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        try {
            $dispatcher = new NotificationDispatcher(
                $this->notificationData['sendVia'] ?? null
            );

            $dispatcher->templateName = $this->notificationData['templateName'];
            $dispatcher->shortCodes = $this->notificationData['shortCodes'] ?? [];
            $dispatcher->user = $this->notificationData['user'] ?? null;
            $dispatcher->createLog = $this->notificationData['createLog'] ?? true;
            $dispatcher->userColumn = $this->notificationData['userColumn'] ?? 'user_id';
            $dispatcher->pushImage = $this->notificationData['pushImage'] ?? null;
            $dispatcher->message = $this->notificationData['message'] ?? null;
            $dispatcher->toAddress = $this->notificationData['toAddress'] ?? null;
            $dispatcher->receiverName = $this->notificationData['receiverName'] ?? null;

            $dispatcher->send();

            Log::info('Notification job completed successfully', [
                'template' => $this->notificationData['templateName']
            ]);

        } catch (\Exception $e) {
            Log::error('Notification job failed in handle method', [
                'data' => $this->notificationData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Notification job permanently failed', [
            'data' => $this->notificationData,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        try {
            AdminNotification::create([
                'user_id' => 0,
                'title' => 'Notification Failed: ' . $exception->getMessage(),
                'click_url' => '#'
            ]);
        } catch (\Exception $e) {
            Log::error('Could not create admin notification for failed job', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }

    public function backoff(): array
    {
        return [10, 30, 60];
    }
}