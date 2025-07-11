<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Closure;

const SORTER_ORDER = [
    'desc' => 'desc',
    'asc' => 'asc',
    'ascend' => 'asc',
    'descend' => 'desc',
    'false' => null
];

trait Queryable
{
    public static function fetch($sorter = null, $search = null, $page = null, $perPage = null, array $searchColumns = [], array|null $filters = [], ?Closure $extras = null)
    {
        return static::query()
            // ->sortBy($sorter['columnKey'] ?? null, SORTER_ORDER[$sorter['order'] ?? null] ?? null)
            // ->sortBy($sorter['columnKey'] ?? 'id', SORTER_ORDER[$sorter['order'] ?? 'ASC'] ?? 'ASC')
            ->sortBy(
                (isset($sorter['order']) && ($sorter['order'] === false || $sorter['order'] === 'false')) ? 'id' : ($sorter['columnKey'] ?? 'id'),
                (isset($sorter['order']) && ($sorter['order'] === false || $sorter['order'] === 'false')) ? 'ASC' : (SORTER_ORDER[$sorter['order'] ?? 'ASC'] ?? 'ASC')
            )
            ->searchBy($searchColumns, $search)
            ->filterBy($filters ?? [])
            ->extend($extras)
            ->selectSelf()
            ->paginate($perPage, page: $page);
    }

    public static function fetchByRequest(Request $request, array $searchColumns = [], ?Closure $extras = null)
    {
        $filters = $request->get('filters', []);

        // Add date_range to filters if it exists
        if ($request->has('date_range') && is_array($request->get('date_range')) && count($request->get('date_range')) === 2) {
            $filters['date_range'] = $request->get('date_range');
        }

        // Filter out empty values
        $filters = array_filter($filters, function ($value) {
            return !in_array($value, ['', [], 'undefined'], true);
        });

        // If search is not empty, remove filters date_range
        if ($request->get('search')) {
            $filters = array_filter($filters, function ($value, $key) {
                return $key !== 'date_range';
            }, ARRAY_FILTER_USE_BOTH);
        }

        return static::fetch(
            sorter: $request->get('sorter'),
            search: $request->get('search'),
            filters: $filters,
            page: $request->get('page') ?? 1,
            perPage: $request->get('pageSize') ?? PHP_INT_MAX,
            searchColumns: $searchColumns,
            extras: $extras
        );
    }

    /**
     * Get query builder for chunking without executing the query
     */
    public static function fetchByRequestQuery(Request $request, array $searchColumns = [], ?Closure $extras = null)
    {
        $filters = $request->get('filters', []);

        // Add date_range to filters if it exists
        if ($request->has('date_range') && is_array($request->get('date_range')) && count($request->get('date_range')) === 2) {
            $filters['date_range'] = $request->get('date_range');
        }

        // Filter out empty values
        $filters = array_filter($filters, function ($value) {
            return !in_array($value, ['', [], 'undefined'], true);
        });

        // If search is not empty, remove filters date_range
        if ($request->get('search')) {
            $filters = array_filter($filters, function ($value, $key) {
                return $key !== 'date_range';
            }, ARRAY_FILTER_USE_BOTH);
        }

        return static::query()
            ->sortBy(
                ($request->get('sorter')['columnKey'] ?? 'id'),
                (SORTER_ORDER[$request->get('sorter')['order'] ?? 'ASC'] ?? 'ASC')
            )
            ->searchBy($searchColumns, $request->get('search'))
            ->filterBy($filters ?? [])
            ->extend($extras)
            ->selectSelf();
    }

    public static function fetchByPagination(Request $request)
    {
        return static::fetch(
            sorter: $request->get('sorter'),
            search: $request->get('search'),
            page: $request->get('page') ?? 1,
            perPage: $request->get('pageSize') ?? PHP_INT_MAX
        );
    }
}
