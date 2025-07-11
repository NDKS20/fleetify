<?php

namespace App\Helpers;

trait HasPermissionRestrictions
{
    public static function getMiddleware()
    {
        return ['protected_route'];
    }
}
