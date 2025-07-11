<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Models\Employee;
use App\Helpers\Error;

class EmployeeService implements ResourceService
{
    public static function getAll($sorter = null, $search = null, $page = null, $perPage = null)
    {
        return Employee::fetch(
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
        return Employee::fetchByRequest($request, ['name', 'phone', 'division.name', 'position'], extras: function ($query) {
            $query->with('division');
        });
    }

    public static function get(string $id): Employee|Error
    {
        try {
            return Employee::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return new Error('Data pegawai tidak ditemukan', 404);
        } catch (Exception $e) {
            Log::error($e);
            return new Error('Terjadi kesalahan', 400);
        }
    }

    public static function store(array $data): Employee|Error
    {
        try {
            DB::beginTransaction();

            // NOTE: Add your custom logic here

            $model = Employee::create($data);

            if (!$model) {
                throw new Exception('Gagal menambahkan data pegawai');
            }

            DB::commit();

            return $model;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data pegawai sudah ada', 422);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return new Error($e->getMessage(), 400);
        }
    }

    public static function update(string $id, array $data): Employee|Error
    {
        try {
            DB::beginTransaction();

            $model = Employee::findOrFail($id);

            // NOTE: Add your custom logic here

            if (!$model->update($data)) {
                throw new Exception('Gagal mengubah data pegawai');
            }

            DB::commit();

            return $model;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data pegawai tidak ditemukan', 404);
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

            $model = Employee::findOrFail($id);

            // NOTE: Add your custom logic here

            if (!$model->delete()) {
                throw new Exception('Gagal menghapus data pegawai');
            }

            DB::commit();

            return null;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data pegawai tidak ditemukan', 404);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return new Error($e->getMessage(), 400);
        }
    }
}
