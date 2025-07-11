<?php

namespace App\Providers;

use Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Closure;
use App\Helpers\RelationHelper;

class BuilderExtensionProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::macro('joinRelation', function (array $relationDetails, $model) {
            /**
             * @var Builder $this
             */
            $relatedTable = $relationDetails['table'];
            $pivot = $relationDetails['pivot'] ?? null;

            if ($pivot) {
                // join related table with pivot table
                return $this->join(
                    $pivot,
                    function ($join) use ($pivot, $model, $relationDetails) {
                        $join->on(
                            $relationDetails['foreignKey'],
                            '=',
                            $model->getTable() . '.' . $model->getKeyName()
                        );

                        if ($pivot === 'model_has_roles') {
                            $join->where($pivot . '.model_type', $relationDetails['model']);
                        }
                    }
                )->join(
                    $relatedTable,
                    $relationDetails['relatedKey'],
                    '=',
                    $relatedTable . '.' . $model->getKeyName()
                );
            }

            return $this->join(
                $relatedTable,
                $relationDetails['foreignKey'],
                '=',
                $relationDetails['ownerKey'] ?? $relationDetails['localKey']
            );
        });

        Builder::macro('selectSelf', function () {
            /**
             * @var Builder $this
             */
            $this->select($this->getModel()->getTable() . '.*');

            return $this;
        });

        Builder::macro('filterBy', function (array $filters) {
            $this->when($filters, function ($query) use ($filters) {
                /**
                 * @var Builder $this
                 */
                $joinedRelations = []; // Track joined relations
                $orConditions = [];

                // First pass: collect OR conditions
                foreach ($filters as $key => $value) {
                    // Log::info($key);
                    // Log::info($value);
                    // Handle OR condition based on "|" prefix and suffix
                    if (is_string($value) && (Str::startsWith($value, '|') || Str::endsWith($value, '|'))) {
                        $orConditions[$key] = trim($value, '|');
                        // Skip this condition in regular processing
                        unset($filters[$key]);
                    }
                }

                foreach ($filters as $key => $value) {
                    $operator = '=';

                    if ($value == "") {
                        continue;
                    }

                    if ($key === 'date_range' && is_array($value) && count($value) === 2) {
                        $startDate = $value[0];
                        $endDate = $value[1];

                        // Fixed: Qualifying the date column with the model's table name
                        $tableName = $this->getModel()->getTable();
                        $this->where($tableName . '.date', '>=', $startDate)
                            ->where($tableName . '.date', '<=', $endDate);

                        continue;
                    }

                    // Handle OR condition based on "|" prefix and suffix dynamically for any column
                    if (is_string($value) && (Str::startsWith($value, '|') || Str::endsWith($value, '|'))) {
                        $trimmedValue = trim($value, '|');

                        $query->where(function ($q) use ($key, $trimmedValue, $value) {
                            if (Str::endsWith($value, '|')) {
                                $q->orWhere($key, '=', $trimmedValue);
                            }
                            if (Str::startsWith($value, '|')) {
                                $q->orWhere($key, '=', $trimmedValue);
                            }
                        });

                        continue;
                    }

                    // Handle array value - MODIFIED: removed the !Str::contains($key, '.') check
                    if (is_array($value)) {
                        // Check if it's a relation field
                        if (Str::contains($key, '.')) {
                            [$relation, $relationKey] = explode('.', $key);

                            // Process relation join
                            if (!in_array($relation, $joinedRelations)) {
                                $func = Str::camel($relation);

                                $relationDetail = RelationHelper::getOneRelationDetails($this->getModel(), $func);
                                if (!$relationDetail) {
                                    continue;
                                }

                                // Join the relation
                                $table = $relationDetail['table'];
                                $query->joinRelation($relationDetail, $this->getModel());

                                // Mark relation as joined
                                $joinedRelations[] = $relation;
                            } else {
                                // Get table name for already joined relation
                                $func = Str::camel($relation);
                                $relationDetail = RelationHelper::getOneRelationDetails($this->getModel(), $func);
                                if (!$relationDetail) {
                                    continue;
                                }
                                $table = $relationDetail['table'];
                            }

                            // Use whereIn with fully qualified column name
                            $query->whereIn($table . '.' . $relationKey, $value);
                        } else {
                            // Regular whereIn for non-relation fields
                            $query->whereIn($key, $value);
                        }
                        continue;
                    }

                    // Handle null value
                    if (is_null($value) || $value === 'null') {
                        $query->whereNull($key);
                        continue;
                    }

                    if (!is_array($value) && Str::contains($value, ':')) {
                        // Check if the key contains 'start_time' or 'end_time'
                        if (Str::contains($key, 'start_time') || Str::contains($key, 'end_time')) {
                            // Handle time format separately
                            [$operator, $time] = explode(':', $value, 2);
                            $value = $time;
                        } else {
                            // Otherwise, handle general operator formatting
                            [$operator, $value] = explode(':', $value);
                            // Whitelist operator
                            $operator = in_array($operator, ['=', '>', '<', '>=', '<=', '!=', '<>', 'like'])
                                ? $operator
                                : '=';
                        }
                    }

                    if (Str::contains($key, '.')) {
                        [$relation, $key] = explode('.', $key);

                        // Check if relation has already been joined
                        if (!in_array($relation, $joinedRelations)) {
                            $func = Str::camel($relation);

                            $relationDetail = RelationHelper::getOneRelationDetails($this->getModel(), $func);
                            if (!$relationDetail) {
                                continue;
                            }

                            // Join the relation if not already joined
                            $table = $relationDetail['table'];
                            $query->joinRelation($relationDetail, $this->getModel());

                            // Mark this relation as joined
                            $joinedRelations[] = $relation;
                        } else {
                            // Get table for existing relation
                            $func = Str::camel($relation);
                            $relationDetail = RelationHelper::getOneRelationDetails($this->getModel(), $func);
                            if ($relationDetail) {
                                $table = $relationDetail['table'];
                            } else {
                                continue;
                            }
                        }
                    } else {
                        $table = $this->getModel()->getTable();
                    }

                    // Ensure the column name is prefixed with the table name
                    $key = $table . '.' . $key;

                    if ($operator === '><' || $operator === '!><') {
                        [$from, $to] = explode(',', $value);

                        if ($operator === '><') {
                            $query->whereBetween($key, [$from, $to]);
                        } else {
                            $query->whereNotBetween($key, [$from, $to]);
                        }

                        continue;
                    }

                    // Check for null or not-null operators
                    if ($operator === 'is' && $value === 'null') {
                        $query->whereNull($key);
                    } elseif ($operator === 'is' && $value === 'not null') {
                        $query->whereNotNull($key);
                    } else {
                        $query->where($key, $operator, $value);
                    }
                }

                // Process OR conditions globally if any exist
                if (!empty($orConditions)) {
                    $query->where(function ($q) use ($orConditions) {
                        $first = true;
                        foreach ($orConditions as $key => $value) {
                            if ($first) {
                                $q->where($key, '=', $value);
                                $first = false;
                            } else {
                                $q->orWhere($key, '=', $value);
                            }
                        }
                    });
                }
            });

            // Log::info($this->toSql());
            // Log::info($this->getBindings());

            return $this;
        });

        Builder::macro('sortBy', function (?string $columnKey, string|null $direction = 'asc') {
            $this->when(!empty($columnKey) && !empty($direction), function (Builder $query) use ($columnKey, $direction) {
                /**
                 * @var Builder $this
                 */
                static $joinedAliases = []; // Track joined table aliases

                if (Str::contains($columnKey, '.')) {
                    $parts = explode('.', $columnKey);
                    $column = array_pop($parts); // Get the final column name
                    $relationPath = $parts; // The remaining parts are the relation path

                    // Process each relation in the path
                    $model = $this->getModel();
                    $currentPath = '';

                    foreach ($relationPath as $index => $relationName) {
                        $func = Str::camel($relationName);
                        $currentPath = $currentPath ? "$currentPath.$func" : $func;

                        // Retrieve relation details
                        $relationDetail = RelationHelper::getOneRelationDetails($model, $func);
                        if (!$relationDetail) {
                            return $this;
                        }

                        $table = $relationDetail['table'];

                        // Generate a unique alias for the join
                        $joinAlias = $relationDetail['pivot'] ?? $table;
                        $joinAliasKey = "$currentPath:$joinAlias";

                        // Check if this specific relation path is already joined
                        if (!in_array($joinAliasKey, $joinedAliases)) {
                            $query->joinRelation($relationDetail, $model);
                            $joinedAliases[] = $joinAliasKey; // Track the joined relation path
                        }

                        // Update the model for the next iteration if we have more relations
                        if ($index < count($relationPath) - 1) {
                            $model = $relationDetail['related_model'] ?? null;
                            if (!$model) {
                                return $this;
                            }
                        }

                        // Use the table from the last relation for ordering
                        if ($index === count($relationPath) - 1) {
                            $query->orderBy("$table.$column", $direction);
                        }
                    }
                } else {
                    $query->orderBy($columnKey, $direction);
                }
            });

            return $this;
        });

        Builder::macro('searchBy', function (string|array $columnKey, string|null $searchTerm) {
            $this->when($searchTerm, function ($query) use ($columnKey, $searchTerm) {
                $columns = [];
                $relations = [];
                $relationColumns = [];

                foreach ((array) $columnKey as $key) {
                    if (Str::contains($key, '.')) {
                        $relationParts = explode('.', $key);

                        if (count($relationParts) === 2) {
                            // Single-level relation e.g., "branch.code"
                            [$relation, $column] = $relationParts;
                            $relations[] = $relation;
                            $relationColumns[] = [$relation, $column];
                        } elseif (count($relationParts) === 3) {
                            // Two-level nested relation e.g., "branchShippingRegion.branch.code"
                            [$relation1, $relation2, $column] = $relationParts;
                            $relations[] = "$relation1.$relation2";
                            $relationColumns[] = ["$relation1.$relation2", $column];
                        }

                        continue;
                    }

                    $columns[] = $key;
                }

                $query
                    ->when($relations, function ($query) use ($relations) {
                        $query->with($relations);
                    })
                    ->where(function ($query) use ($columns, $relationColumns, $searchTerm) {
                        foreach ($columns as $key) {
                            $query->orWhere($key, 'like', "%$searchTerm%");
                        }

                        foreach ($relationColumns as [$relation, $key]) {
                            if (Str::contains($relation, '.')) {
                                // Handle two-level relation search
                                [$relation1, $relation2] = explode('.', $relation);
                                $query->orWhereHas($relation1, function ($query) use ($relation2, $key, $searchTerm) {
                                    $query->whereHas($relation2, function ($query) use ($key, $searchTerm) {
                                        $query->where($key, 'like', "%$searchTerm%");
                                    });
                                });
                            } else {
                                // Handle single-level relation search
                                $query->orWhereHas($relation, function ($query) use ($key, $searchTerm) {
                                    $query->where($key, 'like', "%$searchTerm%");
                                });
                            }
                        }
                    });
            });

            return $this;
        });

        Builder::macro('extend', function (Closure|null $extras) {
            $this->when($extras, $extras);

            return $this;
        });
    }
}
