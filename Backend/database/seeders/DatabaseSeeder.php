<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxiRegion;
use App\Models\License;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        License::create([
            'name' => 'PT. Mixinlabs Karya Teknologi',
        ]);

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,

            DepartmentSeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}
