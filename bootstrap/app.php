<?php

use App\Http\Middleware\BanCheck;
use App\Http\Middleware\IsApproved;
use Illuminate\Foundation\Application;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\EnsureEmailIsVerifiedApi;
use App\Http\Middleware\IsRejected;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        
        $middleware->alias([
            'verifiedcustom' => EnsureEmailIsVerifiedApi::class,
            'ensureapproved' => IsApproved::class,
            'ensurerejected' => IsRejected::class,
            'role' => RoleMiddleware::class,
            'ban' => BanCheck::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // You can customize global exception handling here
    })
    ->withCommands([
        // Register console commands here
        \App\Console\Commands\WorkNotificationQueue::class,
        \App\Console\Commands\RestartQueue::class,
        \App\Console\Commands\QueueStatus::class,
        // \App\Console\Commands\CreateNotificationTemplate::class,
        // \App\Console\Commands\ClearNotificationLogs::class,
    ])
    ->create();
