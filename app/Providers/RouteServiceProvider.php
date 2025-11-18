<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Default API limiter (bawaan Laravel)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        // 🔐 Login limiter: maksimal 20 percobaan per menit per IP
        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(20)->by($request->ip()),
            ];
        });

        // 💬 Comments / AJAX limiter: maksimal 60 request per menit per IP
        RateLimiter::for('comments', function (Request $request) {
            return [
                Limit::perMinute(60)->by($request->ip()),
            ];
        });

        // 🌐 General authenticated limiter: maksimal 120 request per menit per IP
        RateLimiter::for('authenticated', function (Request $request) {
            return [
                Limit::perMinute(120)->by($request->ip()),
            ];
        });
    }
}
