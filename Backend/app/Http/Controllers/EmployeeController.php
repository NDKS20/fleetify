<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmployeeService;
use App\Models\Employee;
use App\Helpers\HasPermissionRestrictions;
use App\Helpers\Error;

class EmployeeController extends Controller
{
    use HasPermissionRestrictions;

    const PERMISSIONS = [
        'index' => ['read-employee'],
        'show' => ['read-employee'],
        'store' => ['create-employee'],
        'update' => ['edit-employee'],
        'destroy' => ['delete-employee'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->respond(EmployeeService::getAllByRequest($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->respond(EmployeeService::get($id));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = Employee::validate($request->all());

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $model = EmployeeService::store($data);

        if ($model instanceof Error) {
            return $this->respondError($model);
        }

        return $this->respondWithMessage($model, 'Data pegawai berhasil ditambahkan', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Employee::validateUpdate($id, $request->all());

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $model = EmployeeService::update($id, $data);

        if ($model instanceof Error) {
            return $this->respondError($model);
        }

        return $this->respondWithMessage($model, 'Data pegawai berhasil diubah', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = EmployeeService::delete($id);

        if ($result instanceof Error) {
            return $this->respondError($result);
        }

        return $this->respondWithMessage($result, 'Data pegawai berhasil dihapus', 200);
    }
}
