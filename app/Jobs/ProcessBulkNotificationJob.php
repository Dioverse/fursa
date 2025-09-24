<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Notify\NotificationDispatcher;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Bus\Queueable;

class ProcessBulkNotificationJob implements ShouldQueue
{
    use Queueable;

    public $tries = 2;
    public $timeout = 300;
    
    protected $notifications;
    protected $method;

    public function __construct(array $notifications, string $method)
    {
        $this->notifications = $notifications;
        $this->method = $method;
        $this->onQueue('bulk-notifications');
    }

    public function handle(): void
    {
        try {
            $dispatcher = new NotificationDispatcher();
            $dispatcher->sendBulk($this->notifications, $this->method);

            Log::info('Bulk notification job completed', [
                'method' => $this->method,
                'count' => count($this->notifications)
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk notification job failed', [
                'method' => $this->method,
                'count' => count($this->notifications),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Bulk notification job permanently failed', [
            'method' => $this->method,
            'count' => count($this->notifications),
            'error' => $exception->getMessage()
        ]);
    }
}
