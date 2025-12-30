<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
            $this->app->singleton(\App\Debugbar\ApiRequestsCollector::class, function () {
                return new \App\Debugbar\ApiRequestsCollector();
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('site', getSiteInfo());
            $view->with('adminUser', Auth::guard('admin')->user());
            $view->with('webUser', Auth::guard('web')->user());
        });
        
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class) && \Barryvdh\Debugbar\Facades\Debugbar::isEnabled()) {
            \Barryvdh\Debugbar\Facades\Debugbar::addCollector($this->app->make(\App\Debugbar\ApiRequestsCollector::class));
        }
    }
}
