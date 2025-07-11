<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Models\Division;
use App\Helpers\Error;

class DivisionService implements ResourceService
{
    public static function getAll($sorter = null, $search = null, $page = null, $perPage = null)
    {
        return Division::fetch(
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
        return Division::fetchByRequest($request, ['name']);
    }

    public static function get(string $id): Division|Error
    {
        try {
            return Division::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return new Error('Data divisi tidak ditemukan', 404);
        } catch (Exception $e) {
            Log::error($e);
            return new Error('Terjadi kesalahan', 400);
        }
    }

    public static function store(array $data): Division|Error
    {
        try {
            DB::beginTransaction();

            // NOTE: Add your custom logic here

            $model = Division::create($data);

            if (!$model) {
                throw new Exception('Gagal menambahkan data divisi');
            }

            DB::commit();

            return $model;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data divisi sudah ada', 422);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return new Error($e->getMessage(), 400);
        }
    }

    public static function update(string $id, array $data): Division|Error
    {
        try {
            DB::beginTransaction();

            $model = Division::findOrFail($id);

            // NOTE: Add your custom logic here

            if (!$model->update($data)) {
                throw new Exception('Gagal mengubah data divisi');
            }

            DB::commit();

            return $model;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data divisi tidak ditemukan', 404);
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

            $model = Division::findOrFail($id);

            // NOTE: Add your custom logic here

            if (!$model->delete()) {
                throw new Exception('Gagal menghapus data divisi');
            }

            DB::commit();

            return null;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();

            return new Error('Data divisi tidak ditemukan', 404);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return new Error($e->getMessage(), 400);
        }
    }
}
