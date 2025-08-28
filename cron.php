<?php

// cron.php in public_html
chdir(__DIR__);
require 'vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run the schedule
$kernel->call('schedule:run');

// Clear Laravel cache
$kernel->call('cache:clear');

// Dump Composer autoload
exec('composer dump-autoload');

// Optionally, you can output messages for logging
echo "Schedule run, cache cleared, composer autoload dumped.\n";
