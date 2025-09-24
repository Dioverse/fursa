<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RestartQueue extends Command
{
    protected $signature = 'notification:restart';
    protected $description = 'Restart notification queue workers';

    public function handle()
    {
        $this->call('queue:restart');
        $this->info('Queue workers restarted successfully');
    }
}
