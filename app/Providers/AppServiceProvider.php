<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share parameter 'project' ke semua view (jika ada)
        View::composer('*', function ($view) {
            $project = request()->route('project'); // aman, akan null jika tak ada route
            if ($project !== null) {
                $view->with('project', $project);
            }
        });

        
        if (ENV('APP_ENV') === 'ian_production') {
            
            URL::forceScheme('https');
        }
    }
}
