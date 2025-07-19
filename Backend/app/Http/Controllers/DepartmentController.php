<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DepartmentService;
use App\Models\Department;
use App\Helpers\HasPermissionRestrictions;
use App\Helpers\Error;

class DepartmentController extends Controller
{
    use HasPermissionRestrictions;

    const PERMISSIONS = [
        'index' => ['read-department'],
        'show' => ['read-department'],
        'store' => ['create-department'],
        'update' => ['update-department'],
        'destroy' => ['delete-department'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->respond(DepartmentService::getAllByRequest($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->respond(DepartmentService::get($id));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = Department::validate($request->all());

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $model = DepartmentService::store($data);

        if ($model instanceof Error) {
            return $this->respondError($model);
        }

        return $this->respondWithMessage($model, 'Data departemen berhasil ditambahkan', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Department::validateUpdate($id, $request->all());

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $model = DepartmentService::update($id, $data);

        if ($model instanceof Error) {
            return $this->respondError($model);
        }

        return $this->respondWithMessage($model, 'Data departemen berhasil diubah', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = DepartmentService::delete($id);

        if ($result instanceof Error) {
            return $this->respondError($result);
        }

        return $this->respondWithMessage($result, 'Data departemen berhasil dihapus', 200);
    }
}
