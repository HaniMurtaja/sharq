<?php

use App\Http\Middleware\HandleApiAcceptHeaderJsonResponse;
use App\Http\Middleware\HandleApiJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
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
            'role'                  => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'            => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission'    => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'admin.role'            => \App\Http\Middleware\AdminRoleMiddleware::class,
            'client.role'           => \App\Http\Middleware\ClientRoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

// ... rest of your configuration code

// Set up configuration with proper values
$config = new \Illuminate\Config\Repository();

// Set view configuration
$config->set('view.paths', [
    $app->resourcePath('views'),
]);
$config->set('view.compiled', $app->storagePath('framework/views'));

// Set cache configuration
$config->set('cache.default', 'file');
$config->set('cache.stores.file', [
    'driver' => 'file',
    'path' => $app->storagePath('framework/cache/data'),
]);

// Set database configuration from .env
$config->set('database.default', env('DB_CONNECTION', 'mysql'));
$config->set('database.connections.mysql', [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', ''),
    'username' => env('DB_USERNAME', ''),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
]);

// Basic app configuration
$config->set('app.key', env('APP_KEY'));
$config->set('app.env', env('APP_ENV', 'local'));
$config->set('app.debug', env('APP_DEBUG', true));

// Bind the configured config service
$app->instance('config', $config);

// Register core services in the right order
$coreProviders = [
    \Illuminate\Cache\CacheServiceProvider::class,
    \Illuminate\Filesystem\FilesystemServiceProvider::class,
    \Illuminate\Database\DatabaseServiceProvider::class,
    \Illuminate\View\ViewServiceProvider::class,
];

foreach ($coreProviders as $provider) {
    $app->register($provider);
}

// Register application providers  
$app->register(\App\Providers\AppServiceProvider::class);
$app->register(\App\Providers\RouteServiceProvider::class);

return $app;
