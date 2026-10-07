<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\TriggerFfvbSync;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'super_admin' => SuperAdminMiddleware::class,
            'ffvb.sync' => TriggerFfvbSync::class,
        ]);
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Cron o2switch : * * * * * cd ~/lmvb && php artisan schedule:run >> /dev/null 2>&1
        $schedule->command('lmvb:sync-ffvb')
            ->everyThirtyMinutes()
            ->between('7:00', '23:59')
            ->withoutOverlapping(30);
        $schedule->command('lmvb:sync-ffvb')->dailyAt('03:30')->withoutOverlapping(30);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
