<?php

use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\EnsureIsLecturer;
use App\Http\Middleware\EnsureIsStudent;
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
            'admin' => EnsureIsAdmin::class,
            'lecturer' => EnsureIsLecturer::class,
            'student' => EnsureIsStudent::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
