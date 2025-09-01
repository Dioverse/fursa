<?php

// cron.php in public_html
chdir(__DIR__);
require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->call('schedule:run');
$kernel->call('cache:clear');
print($kernel->call('storage:link'));
