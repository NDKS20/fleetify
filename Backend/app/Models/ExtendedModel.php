<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\UserActivityLog;
use App\Models\Traits\Queryable;
use App\Models\Traits\HasRules;
use App\Models\Traits\ActiveToggle;

class ExtendedModel extends Model
{
    use HasFactory, UserActivityLog, HasRules, Queryable, ActiveToggle;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($model) {
            $model->castAttributes();
        });
    }

    protected function castAttributes()
    {
        foreach ($this->attributes as $key => $value) {
            if (substr($key, -3) === '_id') {
                // $this->casts[$key] = 'integer';
            }
        }
    }
}
