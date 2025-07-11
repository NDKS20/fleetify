<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            // $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            // $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // DB::listen(function ($query) {
        //     $sqlWithBindings = vsprintf(str_replace('?', '%s', $query->sql), array_map(function ($binding) {
        //         return is_numeric($binding) ? $binding : "'{$binding}'";
        //     }, $query->bindings));

        //     Log::info($sqlWithBindings);
        // });
    }
}
