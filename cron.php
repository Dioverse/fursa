<?php

// cron.php in public_html
chdir(__DIR__);
require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run Laravel's scheduler
$kernel->call('schedule:run');