<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Notify\NotificationDispatcher;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Ensure the files service is available before registering our service
        $this->app->bind('notifier', function ($app) {
            return new NotificationDispatcher();
        });
    }

    public function boot()
    {
        // Only boot if the application is fully loaded
        if ($this->app->bound('files')) {
            // Handle queue job failures
            Queue::failing(function ($connectionName, $job, $data) {
                Log::error('Queue job failed', [
                    'connection' => $connectionName,
                    'job' => $job,
                    'data' => $data
                ]);
            });
        }
    }
}