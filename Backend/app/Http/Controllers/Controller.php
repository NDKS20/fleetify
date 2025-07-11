<?php

namespace App\Http\Controllers;

use App\Helpers\HasValidation;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Cache;

class Controller implements HasMiddleware
{
    use HasValidation;

    public static function middleware()
    {
        // Cache the middlewares for 7 days in memory
        return Cache::remember('middlewares:' . static::class, 7 * 24 * 60 * 60, function () {
            $traits = array_keys(class_uses(static::class));
            $middlewares = [];

            foreach ($traits as $trait) {
                if (method_exists($trait, "getMiddleware")) {
                    $middlewares = array_merge($middlewares, $trait::getMiddleware());
                }
            }

            return $middlewares;
        });
    }
}
