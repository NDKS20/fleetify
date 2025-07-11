<?php

namespace App\Helpers;

use Closure;
use Illuminate\Validation\Validator;

trait HasValidation
{
    use Respondable;

    public function withValidation(array|Validator $validators, Closure $continue)
    {
        if ($validators instanceof Validator) {
            $validators = [$validators];
        }

        foreach ($validators as $validator) {
            if ($validator->fails()) {
                return $this->respondError(new Error($validator->first(), 422));
            }
        }

        return $continue($validators);
    }
}
