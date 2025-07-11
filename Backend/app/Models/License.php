<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class License extends ExtendedModel
{
    use SoftDeletes;

    const RULES = [
        'name' => ['required', 'string', 'max:255'],
        'address' => ['nullable', 'string'],
    ];
}
