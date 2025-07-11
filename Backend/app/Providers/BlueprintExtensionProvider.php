<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class BlueprintExtensionProvider extends ServiceProvider
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
        Blueprint::macro('logs', function ($isSoftDelete = true) {
            /**
             * @var Blueprint $this
             */

            // timestamp
            $this->timestampsTz();
            if ($isSoftDelete) {
                $this->softDeletesTz();
            }

            // user log
            $this->unsignedBigInteger('created_by')->nullable();
            $this->unsignedBigInteger('updated_by')->nullable();
            if ($isSoftDelete) {
                $this->unsignedBigInteger('deleted_by')->nullable();
            }
        });

        Blueprint::macro('activation', function () {
            /**
             * @var Blueprint $this
             */

            $this->boolean('is_active')->default(true);
            $this->timestamp('deactivated_at')->nullable();
            $this->unsignedBigInteger('deactivated_by')->nullable();
        });
    }
}
