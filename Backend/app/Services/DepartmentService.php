<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Models\Department;
use App\Helpers\Error;

class DepartmentService implements ResourceService
{
    public static function getAll($sorter = null, $search = null, $page = null, $perPage = null)
    {
        return Department::fetch(
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
        return Department::fetchByRequest($request, ['department_name', 'max_clock_in_time', 'max_clock_out_time']);
    }

    public static function get(string $id): Department|Error
    {
        try {
            return Department::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return new Error('Data departemen tidak ditemukan', 404);
        } catch (Exception $e) {
            Log::error($e);
            return new Error('Terjadi kesalahan', 400);
        }
    }

    public static function store(array $data): Department|Error
    {
        try {
            DB::beginTransaction();

            // NOTE: Add your custom logic here

            $model = Department::create($data);

            if (!$model) {
                throw new Exception('Gagal menambahkan data departemen');
            }

            DB::commit();

            return $model;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data departemen sudah ada', 422);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return new Error($e->getMessage(), 400);
        }
    }

    public static function update(string $id, array $data): Department|Error
    {
        try {
            DB::beginTransaction();

            $model = Department::findOrFail($id);

            // NOTE: Add your custom logic here

            if (!$model->update($data)) {
                throw new Exception('Gagal mengubah data departemen');
            }

            DB::commit();

            return $model;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data departemen tidak ditemukan', 404);
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

            $model = Department::findOrFail($id);

            // NOTE: Add your custom logic here

            if (!$model->delete()) {
                throw new Exception('Gagal menghapus data departemen');
            }

            DB::commit();

            return null;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data departemen tidak ditemukan', 404);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return new Error($e->getMessage(), 400);
        }
    }
}
