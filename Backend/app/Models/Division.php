<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuid;

class Division extends ExtendedModel
{
    use SoftDeletes, HasUuid;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    const RULES = [
        'name' => ['required', 'string', 'max:255'],
    ];

    /**
     * Get the employees for the division.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
