<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends ExtendedModel
{
    use SoftDeletes;

    const RULES = [
        'name' => ['required', 'string', 'max:255'],
        'employee_id' => ['required', 'string', 'max:50'],
        'department_id' => ['required', 'integer', 'exists:departments,id'],
        'address' => ['required', 'string'],
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($data) {
            $currentDate = now();
            $date = $currentDate->format('d');
            $month = $currentDate->format('m');
            $year = $currentDate->format('Y');

            if (empty($data->employee_id)) {
                do {
                    $employeeID = $data->generateEmployeeID($date, $month, $year);
                } while (self::where('employee_id', $employeeID)->exists());

                $data->employee_id = $employeeID;
            }
        });
    }

    public function generateEmployeeID($date, $month, $year)
    {
        $prefix = 'KRY/' . $date . $month . substr($year, -2) . '/';

        $employeeCount = self::whereYear('created_at', $year)->withTrashed()->count();

        $newNumber = $employeeCount + 1;
        $newNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
