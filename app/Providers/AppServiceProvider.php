<?php

namespace App\Providers;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\View\Components\FaqSection;
use Illuminate\Support\Facades\Blade;
use App\Services\InterestTracker;
use Illuminate\Database\Eloquent\Relations\Relation;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Services\InterestTracker::class);

    }

    public function boot(Request $request )
    {
        Relation::morphMap([
            'product' => \App\Models\Product::class,
            'category' => \App\Models\Category::class,
        ]);

        ini_set('max_execution_time', 120);
        Blade::component('faq-section', FaqSection::class);
        Schema::defaultStringLength(191);
        $languages=  getLanguages();
        View::share('languages', $languages);
        app()->instance('languages', $languages);
    }
}
