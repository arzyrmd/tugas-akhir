<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Console\Scheduling\Schedule;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            ThrottleRequests::class, // Tambahkan middleware rate limiting
        ]);
    })
    ->withProviders([
        App\Providers\FilamentServiceProvider::class,
        // kalau ada tambahan lain, tambahkan di sini
    ])
    ->withSchedule(function (Schedule $schedule) {
        // Jalankan auto-cancel setiap jam (lebih masuk akal)
        $schedule->command('orders:auto-cancel')
                ->hourly()
                ->withoutOverlapping()
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/auto-cancel.log'));

        // Dry-run setiap pagi untuk monitoring
        $schedule->command('orders:auto-cancel --dry-run')
                ->dailyAt('09:00')
                ->appendOutputTo(storage_path('logs/auto-cancel-dry-run.log'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
