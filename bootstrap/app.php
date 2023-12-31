<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$app = new App\Classes\Lumen\Application(
    dirname(__DIR__)
);

//$app->withEloquent();
//$app->withFacades();

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->configure('app');

//$app->middleware([
//    //
//]);

$app->register(App\Providers\AppServiceProvider::class);

return $app;
