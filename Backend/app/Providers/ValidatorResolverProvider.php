<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;

class ValidatorResolverProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Validator::resolver(function (Translator $translator, array $data, array $rules, array $messages, array $attributes) {
            return new \App\Helpers\Validator($translator, $data, $rules, $messages, $attributes);
        });
    }
}
