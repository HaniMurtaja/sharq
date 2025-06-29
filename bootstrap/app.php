<?php

use App\Http\Middleware\HandleApiAcceptHeaderJsonResponse;
use App\Http\Middleware\HandleApiJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            /**** OTHER MIDDLEWARE ALIASES ****/
            'localize'              => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'  => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'  => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'        => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,

            'token.auth'            => \App\Http\Middleware\TokenAuthMiddleware::class,
            'force.json'            => HandleApiJsonResponse::class,
            'AcceptHeader'          => HandleApiAcceptHeaderJsonResponse::class,
            'is_online'             => \App\Http\Middleware\CheckOnlineOperator::class,
            'LyveMiddleware'        => \App\Http\Middleware\LyveMiddleware::class,
            'auth'                  => \App\Http\Middleware\Authenticate::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
