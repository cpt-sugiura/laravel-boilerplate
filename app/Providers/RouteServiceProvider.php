<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

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
            ->namespace($this->namespace.'\\Web')
            ->group(base_path('routes/web.php'));
        Route::middleware('web')
            ->namespace($this->namespace.'\\MemberWeb')
            ->group(base_path('routes/member_web.php'));
        Route::middleware('web')
            ->namespace($this->namespace.'\\AdminWeb')
            ->group(base_path('routes/admin_web.php'));
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
            ->middleware('api')
            ->name('member_api.')
            ->namespace($this->namespace.'\\MemberAPI')
            ->group(base_path('routes/member_api.php'));
    }
}
