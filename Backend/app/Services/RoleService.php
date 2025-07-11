<?php

namespace App\Services;

use App\Helpers\Error;
use App\Models\Permission;
use App\Models\Role;
use Arr;
use DB;
use Illuminate\Http\Request;
use Log;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Str;

class RoleService implements ResourceService
{
    public static function getAll($sorter = null, $search = null, $page = null, $perPage = null)
    {
        return Role::fetch(
            sorter: $sorter,
            search: $search,
            page: $page,
            perPage: $perPage,
            searchColumns: ['name']
        );
    }

    public static function getAllByRequest(Request $request)
    {
        return Role::fetchByRequest($request, ['name']);
    }

    public static function get(string $id)
    {
        try {
            return Role::findById($id);
        } catch (RoleDoesNotExist $e) {
            return new Error('Role tidak ditemukan', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return new Error('Terjadi kesalahan', 500);
        }
    }

    public static function store(array $data)
    {
        try {
            DB::beginTransaction();

            $model = Role::create($data);

            $model->syncPermissions($data['access_permissions']);

            DB::commit();

            return $model;
        } catch (RoleAlreadyExists $e) {
            return new Error('Role sudah ada', 400);
        } catch (\Exception $e) {
            Log::error($e);
            return new Error('Terjadi kesalahan', 500);
        } finally {
            DB::rollBack();
        }
    }

    public static function update(string $id, array $data)
    {
        try {
            $role = Role::findById($id);

            if (Role::where('name', $data['name'])->where('id', '!=', $id)->exists()) {
                return new Error('Role sudah ada', 400);
            }

            DB::beginTransaction();

            $role->update($data);

            if (isset($data['access_permissions'])) {
                $role->syncPermissions($data['access_permissions']);
            }

            DB::commit();

            return $role;
        } catch (RoleDoesNotExist $e) {
            return new Error('Role tidak ditemukan', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return new Error('Terjadi kesalahan', 500);
        } finally {
            DB::rollBack();
        }
    }

    public static function delete(string $id)
    {
        try {
            $role = Role::findById($id);

            if ($role->users->count() > 0) {
                return new Error('Role sudah digunakan oleh user', 400);
            }

            $role->delete();

            return;
        } catch (RoleDoesNotExist $e) {
            return new Error('Role tidak ditemukan', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return new Error('Terjadi kesalahan', 500);
        }
    }

    public static function getPermissions()
    {
        $permissons = Permission::all();
        $groupped = $permissons->map(function ($permissions) {
            $name = explode('-', $permissions->name);
            $action = array_shift($name);

            $name = implode('-', $name);

            $permissions->group = __('permission.group.' . $name);
            $permissions->title = __('permission.' . $action) . ' ' . __('permission.group.' . $name);

            return $permissions;
        });

        return $groupped->groupBy('group');
    }
}
