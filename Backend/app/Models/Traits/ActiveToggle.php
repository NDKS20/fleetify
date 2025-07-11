<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait ActiveToggle
{
    protected static function bootActiveToggle()
    {
        static::creating(function ($model) {
            if ($model->isDirty('is_active') && !$model->is_active && empty($model->deactivated_at)) {
                $model->deactivated_at = now();
                $model->deactivated_by = Auth::id();
            }
        });
        static::updating(function ($model) {
            if ($model->isDirty('is_active')) {
                if (!$model->is_active) {
                    $model->deactivated_at = now();
                    $model->deactivated_by = Auth::id();
                } else {
                    $model->deactivated_at = null;
                    $model->deactivated_by = null;
                }
            }
        });
    }
}
