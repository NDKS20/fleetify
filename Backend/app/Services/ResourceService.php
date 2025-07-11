<?php

namespace App\Services;

use Illuminate\Http\Request;

interface ResourceService
{
    public static function getAll();
    public static function getAllByRequest(Request $request);
    public static function get(string $id);
    public static function store(array $data);
    public static function update(string $id, array $data);
    public static function delete(string $id);
}
