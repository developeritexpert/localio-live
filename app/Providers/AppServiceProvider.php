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
        \Illuminate\Pagination\Paginator::useBootstrapFour();
        Relation::morphMap([
            'product' => \App\Models\Product::class,
            'category' => \App\Models\Category::class,
        ]);

        if (! $this->app->runningInConsole()) {
            ini_set('max_execution_time', 120);
        }
        Blade::component('faq-section', FaqSection::class);
        Blade::directive('csrf', function () {
            return '<?php echo \'<input type="hidden" name="_token" value="\'.csrf_token().\'">\'; ?>';
        });
        Schema::defaultStringLength(191);

        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            $languages = \App\Models\Language::where('status', 1)->get();
        } catch (\Throwable $e) {
            $languages = collect();
        }

        View::share('languages', $languages);
        app()->instance('languages', $languages);
        
        try {
            $footer_countries = \App\Models\Country::where('status', 1)->with('language')->get();
        } catch (\Throwable $e) {
            $footer_countries = collect();
        }

        View::share('footer_countries', $footer_countries);
    }
}
