<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceHistory extends ExtendedModel
{
    use SoftDeletes;

    const RULES = [
        'attendance_id' => ['required', 'integer', 'exists:attendances,id'],
        'employee_id' => ['required', 'integer', 'exists:employees,id'],
        'date_attendance' => ['required', 'timestamp'],
        'attendance_type' => ['required', 'tinyInteger', 'in:1,2'],
        'description' => ['nullable', 'text'],
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
