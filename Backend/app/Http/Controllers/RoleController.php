<?php

namespace App\Http\Controllers;

use App\Helpers\Error;
use App\Helpers\HasPermissionRestrictions;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Str;

class RoleController extends Controller
{
    use HasPermissionRestrictions;

    const PERMISSIONS = [
        'index' => ['read-role'],
        'show' => ['read-role'],
        'store' => ['create-role'],
        'update' => ['update-role'],
        'destroy' => ['delete-role'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->respond(RoleService::getAllByRequest($request));
    }

    public function permissions()
    {
        return $this->respond(RoleService::getPermissions());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->respond(RoleService::get($id));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = Role::validate($request->only(['title', 'access_permissions']));

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $role = RoleService::store([
            'title' => $data['title'],
            'name' => Str::slug($data['title']),
            'access_permissions' => $data['access_permissions'],
        ]);

        return $this->respondWithMessage($role, 'Role berhasil ditambahkan', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Role::validateUpdate($id, $request->only(['title', 'access_permissions']));

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        if (isset($data['title'])) {
            $data['name'] = Str::slug($data['title']);
        }

        $role = RoleService::update($id, $data);

        return $this->respondWithMessage($role, 'Role berhasil diubah', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->respondWithMessage(RoleService::delete($id), 'Role berhasil dihapus', 200);
    }
}
