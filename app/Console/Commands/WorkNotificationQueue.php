<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WorkNotificationQueue extends Command
{
    protected $signature = 'notification:work 
                            {--queue=notifications : The queue to work}
                            {--timeout=60 : Timeout for each job}
                            {--sleep=3 : Sleep time between jobs}
                            {--tries=3 : Number of attempts}';
    
    protected $description = 'Start processing notification queue';

    public function handle()
    {
        $queue = $this->option('queue');
        $timeout = $this->option('timeout');
        $sleep = $this->option('sleep');
        $tries = $this->option('tries');

        $this->info("Starting notification queue worker...");
        $this->info("Queue: {$queue}");
        $this->info("Timeout: {$timeout}s");
        $this->info("Sleep: {$sleep}s");
        $this->info("Tries: {$tries}");

        $this->call('queue:work', [
            '--queue' => $queue,
            '--timeout' => $timeout,
            '--sleep' => $sleep,
            '--tries' => $tries,
            '--memory' => 128, // Memory limit in MB
            '--daemon' => true,
        ]);
    }
}