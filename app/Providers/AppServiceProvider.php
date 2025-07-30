<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\HeroSetting;

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
        // Rende disponibili $hero e $renderAnalytics in tutte le view
        View::composer('*', function ($view) {
            $view->with('hero', HeroSetting::first());
            $view->with('renderAnalytics', view('partials.analytics')->render());
        });
    }
}
