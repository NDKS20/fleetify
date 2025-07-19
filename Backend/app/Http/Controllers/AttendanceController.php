<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AttendanceService;
use App\Models\Attendance;
use App\Helpers\HasPermissionRestrictions;
use App\Helpers\Error;

class AttendanceController extends Controller
{
    use HasPermissionRestrictions;

    const PERMISSIONS = [
        'index' => ['read-attendance'],
        'getAttendanceHistory' => ['read-attendance'],
        'getAttendanceByDepartment' => ['read-attendance'],
        'show' => ['read-attendance'],
        'store' => ['create-attendance'],
        'update' => ['update-attendance'],
        'destroy' => ['delete-attendance'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->respond(AttendanceService::getAllByRequest($request));
    }

    public function getAttendanceHistory(Request $request)
    {
        return $this->respond(AttendanceService::getAttendanceHistory($request));
    }

    public function getAttendanceByDepartment(Request $request)
    {
        return $this->respond(AttendanceService::getAttendanceByDepartment($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->respond(AttendanceService::get($id));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = Attendance::validate($request->all());

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $model = AttendanceService::store($data);

        if ($model instanceof Error) {
            return $this->respondError($model);
        }

        return $this->respondWithMessage($model, 'Data kehadiran berhasil ditambahkan', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Attendance::validateUpdate($id, $request->all());

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $model = AttendanceService::update($id, $data);

        if ($model instanceof Error) {
            return $this->respondError($model);
        }

        return $this->respondWithMessage($model, 'Data kehadiran berhasil diubah', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = AttendanceService::delete($id);

        if ($result instanceof Error) {
            return $this->respondError($result);
        }

        return $this->respondWithMessage($result, 'Data kehadiran berhasil dihapus', 200);
    }
}
