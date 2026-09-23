<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $publicPath = config('app.public_path');

        if (is_string($publicPath) && $publicPath !== '') {
            $this->app->usePublicPath($publicPath);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        View::composer('app', function ($view) {
            $view->with('seoDefaults', \App\Support\PageMeta::load());
        });
    }
}
