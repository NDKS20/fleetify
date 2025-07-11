<?php

namespace Illuminate\Database\Schema {
    class Blueprint
    {
        /**
         * Create logging columns, eg: created_by, updated_by, deleted_by, including timestamps.
         * @param  bool  $isSoftDelete
         * @return void
         */
        public function logs($isSoftDelete = true)
        {
            return;
        }

        /**
         * Create activation columns, eg: is_active, activated_at, activated_by.
         * @return void
         */
        public function activation()
        {
            return;
        }
    }
}

namespace Illuminate\Database\Eloquent {

    use Closure;

    class Builder
    {
        /**
         * Add a sorting mechanism to the query.
         *
         * @param  string  $columnKey
         * @param  string  $direction
         * @return $this
         */
        public function sortBy(string $columnKey, string $direction = 'asc')
        {
            return $this;
        }

        /**
         * Add a search mechanism to the query.
         *
         * @param  string|array  $columnKey
         * @param  string  $searchTerm
         * @return $this
         */
        public function searchBy(string|array $columnKey, string $searchTerm)
        {
            return $this;
        }

        /**
         * Extend the query with custom logic.
         *
         * @param  \Closure  $extras
         * @return $this
         */
        public function extend(Closure $extras)
        {
            return $this;
        }
    }
}

namespace Illuminate\Validation {
    class Validator
    {
        /**
         * Get the validated data or returns an error.
         *
         * @return array|Error
         */
        public function validatedOrError()
        {
            return [];
        }
    }
}
