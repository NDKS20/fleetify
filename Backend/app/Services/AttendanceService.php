<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Carbon\Carbon;
use App\Models\AttendanceHistory;
use App\Models\Attendance;
use App\Helpers\Error;

class AttendanceService implements ResourceService
{
    public static function getAll($sorter = null, $search = null, $page = null, $perPage = null)
    {
        return Attendance::fetch(
            sorter: $sorter,
            search: $search,
            page: $page,
            perPage: $perPage,
            // TODO: Add searchColumns
            searchColumns: [''],
        );
    }

    public static function getAllByRequest(Request $request)
    {
        // TODO: Add searchColumns
        return Attendance::fetchByRequest($request, ['employee_id', 'attendance_id', 'employee.name', 'employee.department.name'], fn($query) => $query->with('employee.department'));
    }

    public static function getAttendanceHistory(Request $request)
    {
        $attendanceHistories = AttendanceHistory::fetchByRequest(
            $request,
            ['employee_id', 'attendance_id', 'employee.name', 'employee.department.name'],
            fn($query) => $query->with(['employee.department', 'attendance'])
        );

        // Add punctuality data to each attendance history record
        if (isset($attendanceHistories['data'])) {
            foreach ($attendanceHistories['data'] as &$attendanceHistory) {
                $attendanceHistory['punctuality'] = self::calculatePunctualityStatus($attendanceHistory);
            }
        }

        return $attendanceHistories;
    }

    public static function getAttendanceByDepartment(Request $request)
    {
        // Get all attendance records with employee, department data, and their attendance histories
        $attendances = Attendance::with(['employee.department', 'attendanceHistories'])
            ->whereHas('employee.department')
            ->get();

        // Group data by department
        $departmentData = [];

        // Process attendances with their related attendance histories
        foreach ($attendances as $attendance) {
            $departmentName = $attendance->employee->department->department_name;

            if (!isset($departmentData[$departmentName])) {
                $departmentData[$departmentName] = [
                    'department_info' => [
                        'id' => $attendance->employee->department->id,
                        'department_name' => $departmentName,
                        'max_clock_in_time' => $attendance->employee->department->max_clock_in_time,
                        'max_clock_out_time' => $attendance->employee->department->max_clock_out_time,
                    ],
                    'attendance' => [],
                    'status' => [
                        'total_records' => 0,
                        'tepat_waktu' => 0,
                        'terlambat' => 0,
                        'terlalu_lama' => 0,
                        'punctuality_rate' => 0,
                    ]
                ];
            }

            // Convert attendance to array and process its attendance histories
            $attendanceArray = $attendance->toArray();

            // Process each attendance history for this attendance record
            if (isset($attendanceArray['attendance_histories'])) {
                foreach ($attendanceArray['attendance_histories'] as &$history) {
                    // Add employee and department data to history for punctuality calculation
                    $history['employee'] = $attendanceArray['employee'];

                    // Calculate punctuality for each history record
                    $punctuality = self::calculatePunctualityStatus($history);
                    $history['punctuality'] = $punctuality;

                    // Update status counters
                    $departmentData[$departmentName]['status']['total_records']++;
                    $departmentData[$departmentName]['status'][$punctuality['status']]++;
                }
            }

            $departmentData[$departmentName]['attendance'][] = $attendanceArray;
        }

        // Calculate punctuality rates for each department
        foreach ($departmentData as $departmentName => &$data) {
            $total = $data['status']['total_records'];
            if ($total > 0) {
                $onTime = $data['status']['tepat_waktu'];
                $data['status']['punctuality_rate'] = round(($onTime / $total) * 100, 2);
            }
        }

        return $departmentData;
    }

    /**
     * Calculate punctuality status for attendance history record
     */
    public static function calculatePunctualityStatus($attendanceHistory)
    {
        if (!isset($attendanceHistory['employee']['department'])) {
            return [
                'status' => 'unknown',
                'message' => 'Departemen tidak ditemukan'
            ];
        }

        $department = $attendanceHistory['employee']['department'];
        $attendanceTime = \Carbon\Carbon::parse($attendanceHistory['date_attendance']);
        $attendanceTimeOnly = $attendanceTime->format('H:i:s');

        // Check punctuality based on attendance type
        if ($attendanceHistory['attendance_type'] == 1) {
            // Check-in punctuality
            $maxClockInTime = $department['max_clock_in_time'];

            if ($attendanceTimeOnly <= $maxClockInTime) {
                return [
                    'status' => 'tepat_waktu',
                    'message' => 'Tepat waktu',
                    'max_time' => $maxClockInTime,
                    'actual_time' => $attendanceTimeOnly,
                    'type' => 'masuk'
                ];
            } else {
                $maxTime = Carbon::parse($maxClockInTime);
                $actualTime = Carbon::parse($attendanceTimeOnly);
                $lateDuration = $actualTime->diff($maxTime);

                return [
                    'status' => 'terlambat',
                    'message' => 'Terlambat masuk',
                    'max_time' => $maxClockInTime,
                    'actual_time' => $attendanceTimeOnly,
                    'late_duration' => $lateDuration->format('%H:%I:%S'),
                    'type' => 'masuk'
                ];
            }
        } elseif ($attendanceHistory['attendance_type'] == 2) {
            // Check-out punctuality
            $maxClockOutTime = $department['max_clock_out_time'];

            if ($attendanceTimeOnly <= $maxClockOutTime) {
                return [
                    'status' => 'tepat_waktu',
                    'message' => 'Tepat waktu',
                    'max_time' => $maxClockOutTime,
                    'actual_time' => $attendanceTimeOnly,
                    'type' => 'keluar'
                ];
            } else {
                $maxTime = Carbon::parse($maxClockOutTime);
                $actualTime = Carbon::parse($attendanceTimeOnly);
                $earlyDuration = $maxTime->diff($actualTime);

                return [
                    'status' => 'terlalu_lama',
                    'message' => 'Keluar terlalu lama',
                    'max_time' => $maxClockOutTime,
                    'actual_time' => $attendanceTimeOnly,
                    'early_duration' => $earlyDuration->format('%H:%I:%S'),
                    'type' => 'keluar'
                ];
            }
        }

        return [
            'status' => 'unknown',
            'message' => 'Tipe absensi tidak dikenali'
        ];
    }

    public static function get(string $id): Attendance|Error
    {
        try {
            return Attendance::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return new Error('Data kehadiran tidak ditemukan', 404);
        } catch (Exception $e) {
            Log::error($e);
            return new Error('Terjadi kesalahan', 400);
        }
    }

    public static function store(array $data): Attendance|Error
    {
        try {
            DB::beginTransaction();

            $data['employee_id'] = Auth::user()->employee_id;

            $model = Attendance::create($data);

            if (!$model) {
                throw new Exception('Gagal menambahkan data kehadiran');
            }

            $createHistory = AttendanceHistory::create([
                'attendance_id' => $model->id,
                'employee_id' => $model->employee_id,
                'date_attendance' => $model->clock_in,
                'attendance_type' => 1,
                'description' => 'Absen masuk',
            ]);

            if (!$createHistory) {
                throw new Exception('Gagal menambahkan data histori kehadiran');
            }

            DB::commit();

            return $model;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data kehadiran sudah ada', 422);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return new Error($e->getMessage(), 400);
        }
    }

    public static function update(string $id, array $data): Attendance|Error
    {
        try {
            DB::beginTransaction();

            $model = Attendance::findOrFail($id);

            $createHistory = AttendanceHistory::create([
                'attendance_id' => $model->id,
                'employee_id' => $model->employee_id,
                'date_attendance' => $model->clock_out,
                'attendance_type' => 2,
                'description' => 'Absen keluar',
            ]);

            if (!$createHistory) {
                throw new Exception('Gagal menambahkan data histori kehadiran');
            }

            if (!$model->update($data)) {
                throw new Exception('Gagal mengubah data kehadiran');
            }

            DB::commit();

            return $model;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data kehadiran tidak ditemukan', 404);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return new Error($e->getMessage(), 400);
        }
    }

    public static function delete(string $id): null|Error
    {
        try {
            DB::beginTransaction();

            $model = Attendance::findOrFail($id);

            // NOTE: Add your custom logic here

            if (!$model->delete()) {
                throw new Exception('Gagal menghapus data kehadiran');
            }

            DB::commit();

            return null;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data kehadiran tidak ditemukan', 404);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return new Error($e->getMessage(), 400);
        }
    }
}
