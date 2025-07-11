<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuid;

class Employee extends ExtendedModel
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
        'phone' => ['required', 'string', 'max:255'],
        'image' => ['nullable', 'string'],
        'division_id' => ['required', 'string', 'exists:divisions,id'],
        'position' => ['required', 'string', 'max:255'],
    ];

    /**
     * Get the division that the employee belongs to.
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
