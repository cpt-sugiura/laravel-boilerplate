<?php

namespace App\Providers;

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = '';

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->name('web.')
            ->group(base_path('routes/web.php'));
        Route::middleware('web')
            ->name('admin_web.')
            ->prefix('admin')
            ->group(base_path('routes/admin_web.php'));
        Route::middleware(['web'])
            ->name('member_web.')
            ->group(base_path('routes/member_web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->name('api.')
            ->group(base_path('routes/api.php'));
        Route::prefix('user-browser-api')
            ->middleware(['api', SetLocale::class])
            ->name('browser-api.')
            ->group(base_path('routes/member_api.php'));
    }
}
