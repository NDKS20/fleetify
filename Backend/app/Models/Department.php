<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends ExtendedModel
{
    use SoftDeletes;

    const RULES = [
        'department_name' => ['required', 'string', 'max:255'],
        'max_clock_in_time' => ['required', 'date_format:H:i:s'],
        'max_clock_out_time' => ['required', 'date_format:H:i:s'],
    ];

    /**
     * Get the employees for the department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
