<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ObserverServiceProvider
 * @package App\Providers
 */
class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     *　基本的には observe(Observer::class); を列挙.
     * @return void
     */
    public function boot(): void
    {
    }
}
