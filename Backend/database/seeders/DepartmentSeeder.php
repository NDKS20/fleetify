<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'IT',
            'HRD',
            'Finance',
            'Marketing',
            'Sales',
            'Customer Service',
            'Production',
            'Warehouse',
        ];

        foreach ($departments as $department) {
            Department::create([
                'department_name' => $department,
                'max_clock_in_time' => '08:00:00',
                'max_clock_out_time' => '17:00:00',
            ]);
        }
    }
}
