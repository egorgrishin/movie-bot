<?php

namespace App\Providers;

use App\Classes\Request;
use App\Start;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

/**
 * @property Application $app
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        $this->app->bind(Request::class, fn () => Request::capture());
        $this->app->router->post(
            env('BOT_ROUTE'),
            ['uses' => Start::class]
        );
    }
}
