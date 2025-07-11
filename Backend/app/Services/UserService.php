<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Models\User;
use App\Helpers\Error;

class UserService implements ResourceService
{
    public static function getAll($sorter = null, $search = null, $page = null, $perPage = null)
    {
        return User::fetch(
            sorter: $sorter,
            search: $search,
            page: $page,
            perPage: $perPage,
            searchColumns: ['username', 'name']
        );
    }

    public static function getAllByRequest(Request $request)
    {
        return User::fetchByRequest($request, ['username', 'name']);
    }

    public static function get(string $id)
    {
        try {
            return User::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return new Error('User not found', 404);
        } catch (Exception $e) {
            Log::error($e);
            return new Error('An error occurred', 500);
        }
    }

    public static function store(array $data)
    {
        try {
            DB::beginTransaction();

            $model = User::create(Arr::except($data, ['role']));
            $model->syncRoles($data['role']);

            DB::commit();

            return $model;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return new Error('User already exists', 400);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return new Error('An error occurred', 500);
        }
    }

    public static function update(string $id, array $data)
    {
        try {
            $user = User::findOrFail($id);

            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            DB::beginTransaction();

            $user->update(Arr::except($data, ['role']));

            if (isset($data['role'])) {
                $user->syncRoles($data['role']);
            }

            DB::commit();

            return $user;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return new Error('User not found', 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return new Error('An error occurred', 500);
        }
    }

    public static function delete(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return;
        } catch (ModelNotFoundException $e) {
            return new Error('User not found', 404);
        } catch (Exception $e) {
            Log::error($e);
            return new Error('An error occurred', 500);
        }
    }
}
