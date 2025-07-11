<?php

namespace App\Helpers;

use Illuminate\Validation\Validator as IlluminateValidator;

class Validator extends IlluminateValidator
{
    public function validatedOrError()
    {
        if ($this->fails()) {
            return new Error($this->errors(), 422);
        }

        return $this->validated();
    }
}
