<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Attendance extends ExtendedModel
{
    use SoftDeletes;

    const RULES = [
        'clock_in' => ['required', 'date_format:Y-m-d\TH:i:s.v\Z'],
        'clock_out' => ['nullable', 'date_format:Y-m-d\TH:i:s.v\Z'],
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    /**
     * Convert ISO 8601 format to datetime for clock_in (UTC to UTC+7)
     */
    public function setClockInAttribute($value)
    {
        if ($value && is_string($value)) {
            // Parse the UTC timestamp and convert to UTC+7 (Asia/Jakarta)
            $this->attributes['clock_in'] = Carbon::parse($value)
                ->utc()
                ->setTimezone('Asia/Jakarta')
                ->format('Y-m-d H:i:s');
        } else {
            $this->attributes['clock_in'] = $value;
        }
    }

    /**
     * Convert ISO 8601 format to datetime for clock_out (UTC to UTC+7)
     */
    public function setClockOutAttribute($value)
    {
        if ($value && is_string($value)) {
            // Parse the UTC timestamp and convert to UTC+7 (Asia/Jakarta)
            $this->attributes['clock_out'] = Carbon::parse($value)
                ->utc()
                ->setTimezone('Asia/Jakarta')
                ->format('Y-m-d H:i:s');
        } else {
            $this->attributes['clock_out'] = $value;
        }
    }

    /**
     * Return clock_in as UTC+7 timestamp
     */
    public function getClockInAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value, 'Asia/Jakarta');
        }
        return $value;
    }

    /**
     * Return clock_out as UTC+7 timestamp
     */
    public function getClockOutAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value, 'Asia/Jakarta');
        }
        return $value;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($data) {
            $currentDate = now();
            $date = $currentDate->format('d');
            $month = $currentDate->format('m');
            $year = $currentDate->format('Y');

            if (empty($data->attendance_id)) {
                do {
                    $attendanceID = $data->generateAttendanceID($date, $month, $year);
                } while (self::where('attendance_id', $attendanceID)->exists());

                $data->attendance_id = $attendanceID;
            }
        });
    }

    public function generateAttendanceID($date, $month, $year)
    {
        $prefix = 'ATT/' . $date . $month . substr($year, -2) . '/';

        $attendanceCount = self::whereYear('created_at', $year)->withTrashed()->count();

        $newNumber = $attendanceCount + 1;
        $newNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function attendanceHistories()
    {
        return $this->hasMany(AttendanceHistory::class, 'attendance_id');
    }
}
