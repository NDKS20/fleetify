<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\User;
use App\Helpers\HasPermissionRestrictions;
use App\Helpers\Error;

class UserController extends Controller
{
    use HasPermissionRestrictions;

    const PERMISSIONS = [
        'index' => ['read-user'],
        'show' => ['read-user'],
        'store' => ['create-user'],
        'update' => ['update-user'],
        'destroy' => ['delete-user'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->respond(UserService::getAllByRequest($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->respond(UserService::get($id));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = User::validate($request->only([
            'name',
            'role',
            'username',
            'password',
            'is_active',
        ]));

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $user = UserService::store($data);

        return $this->respondWithMessage($user, 'User created successfully', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = User::validateUpdate($id, $request->only([
            'name',
            'role',
            'username',
            'password',
            'is_active',
        ]));

        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $user = UserService::update($id, $data);

        return $this->respondWithMessage($user, 'User updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->respondWithMessage(UserService::delete($id), 'User deleted successfully', 200);
    }
}
