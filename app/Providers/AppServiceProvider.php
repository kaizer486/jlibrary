<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Services\WebSearchService;     
use App\Ai\Tools\WebSearchTool;   
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
  public function register(): void
    {
         $this->app->singleton(WebSearchService::class);
        $this->app->singleton(WebSearchTool::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (config('app.env') === 'production') {
        \URL::forceScheme('https');
    }
    }
}
