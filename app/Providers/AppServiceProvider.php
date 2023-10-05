<?php

namespace App\Providers;

use App\Classes\Lumen\Application;
use App\Classes\Lumen\Http\Request;
use App\Start;
use Illuminate\Support\ServiceProvider;

/**
 * @property Application $app
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     */
    public function register(): void
    {
        $this->app->bind(Request::class, fn () => Request::capture());
        $this->app->router->post(
            env('BOT_ROUTE'),
            ['uses' => Start::class]
        );
    }
}
