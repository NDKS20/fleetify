<?php

namespace App\Models\Traits;

use App\Helpers\Error;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait HasRules
{
    public static function getRulesFor($data, $overrides = [], $fields = [])
    {
        $rules = static::getRules($fields);

        if (!empty($overrides)) {
            $rules = array_merge($rules, $overrides);
        }

        foreach ($rules as $k => $v) {
            $rules[$k] = static::getValidationRules($k, $v, $data);
        }

        return $rules;
    }

    public static function validateOnly(array $fields, $data, $overrides = [])
    {
        $rules = static::getRulesFor($data, $overrides, $fields);

        return Validator::make($data, $rules)->validatedOrError();
    }

    public static function validate($data, $overrides = [])
    {
        $rules = static::getRulesFor($data, $overrides);
        $dataOrError = Validator::make($data, $rules)->validatedOrError();

        if ($dataOrError instanceof Error) {
            $dataOrError->body = $dataOrError->body->first();
        }

        return $dataOrError;
    }

    public static function validateUpdate($id, $data, $overrides = [])
    {
        $data['id'] = $id;
        $rules = static::getRulesFor($data, $overrides);

        foreach ($rules as $k => $v) {
            if (in_array('required', $v)) {
                $rules[$k] = array_diff($v, ['required']);
            }
        }

        $dataOrError = Validator::make($data, $rules)->validatedOrError();

        if ($dataOrError instanceof Error) {
            $dataOrError->body = $dataOrError->body->first();
        }

        return $dataOrError;
    }

    protected static function getRules($fields = []): array
    {
        /** @disregard P1012 */
        $staticRules = defined('static::RULES') ? static::RULES : [];

        if (!empty($fields)) {
            $rules = [];
            foreach ($fields as $field) {
                $rules[$field] = $staticRules[$field] ?? [];
            }

            return $rules;
        }

        return $staticRules;
    }

    protected static function getValidationRules($field, $rules, $data)
    {
        if (!empty($rules['type'])) {
            $type = $rules['type'];

            if ($type == 'email') {
                $type .= ':filter';
                $rules[] = 'lowercase';
            }

            if ($type == 'timestamp') {
                $type = 'date';
            }

            $rules[] = $type;
            unset($props['type']);
        }

        if (!empty($rules['unique'])) {
            $rules[] = Rule::unique(static::class, $field)->ignore($data['id'] ?? null)->withoutTrashed();
            unset($rules['unique']);
        }

        if (!empty($rules['in'])) {
            $rules[] = Rule::in($rules['in']);
            unset($rules['in']);
        }

        return $rules;
    }
}
