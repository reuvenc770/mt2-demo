<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->configureMonologUsing(function ($monolog) {

    $formatter = new \Monolog\Formatter\LineFormatter(null, null, true, true);

    $stream = new \Monolog\Handler\RotatingFileHandler(storage_path('logs/warning_notice_debug.log'),5,\Monolog\Logger::DEBUG,false);
    $stream->setFormatter($formatter);
    $monolog->pushHandler($stream);
    
    $stream = new \Monolog\Handler\RotatingFileHandler(storage_path('logs/information_log.log'),5,\Monolog\Logger::INFO, false);
    $stream->setFormatter($formatter);
    $monolog->pushHandler($stream);

    $stream = new \Monolog\Handler\RotatingFileHandler(storage_path('logs/warning_notice_debug.log'),5,\Monolog\Logger::NOTICE,false);
    $stream->setFormatter($formatter);
    $monolog->pushHandler($stream);

    $stream = new \Monolog\Handler\RotatingFileHandler(storage_path('logs/warning_notice_debug.log'),5,\Monolog\Logger::WARNING,false);
    $stream->setFormatter($formatter);
    $monolog->pushHandler($stream);

    $stream = new \Monolog\Handler\RotatingFileHandler(storage_path('logs/errors.log'),5,\Monolog\Logger::ERROR,false);
    $stream->setFormatter($formatter);
    $monolog->pushHandler($stream);

    $stream = new \Monolog\Handler\StreamHandler(storage_path('logs/critical_notices.log'),\Monolog\Logger::CRITICAL,false);
    $stream->setFormatter($formatter);
    $monolog->pushHandler($stream);

    $stream = new \Monolog\Handler\StreamHandler(storage_path('logs/alert_notices.log'),\Monolog\Logger::ALERT,false);
    $stream->setFormatter($formatter);
    $monolog->pushHandler($stream);

    $stream = new \Monolog\Handler\StreamHandler(storage_path('logs/emergency_notices.log'),\Monolog\Logger::EMERGENCY,false);
    $stream->setFormatter($formatter);
    $monolog->pushHandler($stream);


});


/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
