<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DivisionService;
use App\Models\Division;
use App\Helpers\HasPermissionRestrictions;
use App\Helpers\Error;

class DivisionController extends Controller
{
    use HasPermissionRestrictions;

    const PERMISSIONS = [
        'index' => ['read-division'],
        'show' => ['read-division'],
        'store' => ['create-division'],
        'update' => ['edit-division'],
        'destroy' => ['delete-division'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->respond(DivisionService::getAllByRequest($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->respond(DivisionService::get($id));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = Division::validate($request->all());

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $model = DivisionService::store($data);

        if ($model instanceof Error) {
            return $this->respondError($model);
        }

        return $this->respondWithMessage($model, 'Data divisi berhasil ditambahkan', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Division::validateUpdate($id, $request->all());

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $model = DivisionService::update($id, $data);

        if ($model instanceof Error) {
            return $this->respondError($model);
        }

        return $this->respondWithMessage($model, 'Data divisi berhasil diubah', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = DivisionService::delete($id);

        if ($result instanceof Error) {
            return $this->respondError($result);
        }

        return $this->respondWithMessage($result, 'Data divisi berhasil dihapus', 200);
    }
}
