<?php

namespace App\Helpers;

class RelationHelper
{
    public static function getOneRelationDetails($model, $relation)
    {
        if (!method_exists($model, $relation)) {
            return null;
        }

        $relationInstance = $model->{$relation}();
        $tableName = $model->getTable();

        if ($relationInstance instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
            $relatedTable = $relationInstance->getRelated()->getTable();
            $foreignKey = $relationInstance->getForeignKeyName();
            $ownerKey = $relationInstance->getOwnerKeyName();

            return [
                'table' => $relatedTable,
                'foreignKey' => "$tableName.$foreignKey",
                'ownerKey' => "$relatedTable.$ownerKey"
            ];
        } elseif ($relationInstance instanceof \Illuminate\Database\Eloquent\Relations\HasOne || $relationInstance instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
            $relatedTable = $relationInstance->getRelated()->getTable();
            $foreignKey = $relationInstance->getForeignKeyName();
            $localKey = $relationInstance->getLocalKeyName();

            return [
                'table' => $relatedTable,
                'foreignKey' => "$relatedTable.$foreignKey",
                'localKey' => "$tableName.$localKey"
            ];
        } elseif ($relationInstance instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
            $relatedTable = $relationInstance->getRelated()->getTable();
            $pivotTable = $relationInstance->getTable();
            $foreignKey = $relationInstance->getQualifiedForeignPivotKeyName();
            $relatedKey = $relationInstance->getQualifiedRelatedPivotKeyName();

            return [
                'model' => $model::class,
                'pivot' => $pivotTable,
                'table' => $relatedTable,
                'foreignKey' => $foreignKey,
                'relatedKey' => $relatedKey
            ];
        }

        return null;
    }
}
