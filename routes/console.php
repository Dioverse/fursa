<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

    Artisan::command('queue:worker-safe', function () {
        $this->info('Starting queue worker...');

        // Start the worker with 3 tries, delay 2s, and auto-stop when queue is empty
        // "--once" ensures it won’t loop infinitely (so scheduler controls it)
        $this->call('queue:work', [
            '--tries'           => 3,
            '--delay'           => 2,
            '--memory'          => 128,
             '--timeout'        => 60,
            '--stop-when-empty' => true,
        ]);
    })->describe('Start a queue worker safely with retries and no duplicates');

    // Schedule the command
    Schedule::command('queue:worker-safe')
        ->everyMinute()
        ->withoutOverlapping()
        ->runInBackground();