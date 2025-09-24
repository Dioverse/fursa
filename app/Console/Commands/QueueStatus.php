<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueueStatus extends Command
{
    protected $signature = 'notification:status';
    protected $description = 'Show notification queue status';

    public function handle()
    {
        // Show pending jobs
        $pendingJobs = DB::table('jobs')->where('queue', 'notifications')->count();
        $bulkJobs = DB::table('jobs')->where('queue', 'bulk-notifications')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        $this->info("Queue Status:");
        $this->table(['Queue', 'Pending Jobs'], [
            ['notifications', $pendingJobs],
            ['bulk-notifications', $bulkJobs],
            ['failed', $failedJobs],
        ]);

        if ($failedJobs > 0) {
            $this->warn("You have {$failedJobs} failed jobs. Run 'php artisan queue:retry all' to retry them.");
        }
    }
}
