<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Indonesian locale for more realistic names

        // Get all department IDs
        $departmentIds = Department::pluck('id')->toArray();

        // Check if departments exist
        if (empty($departmentIds)) {
            $this->command->error('No departments found. Please run DepartmentSeeder first.');
            return;
        }

        $admin = User::where('username', 'admin')->first();

        $addAdminToEmployee = Employee::create([
            'name' => $admin->name,
            'department_id' => $faker->randomElement($departmentIds),
            'address' => $faker->address(),
        ]);

        $admin->employee_id = $addAdminToEmployee->employee_id;
        $admin->save();

        $employee = User::where('username', 'employee')->first();

        $addEmployeeToEmployee = Employee::create([
            'name' => $employee->name,
            'department_id' => $faker->randomElement($departmentIds),
            'address' => $faker->address(),
        ]);

        $employee->employee_id = $addEmployeeToEmployee->employee_id;
        $employee->save();

        // Create 50 fake employees
        for ($i = 0; $i < 50; $i++) {
            $employee = Employee::create([
                'name' => $faker->name(),
                'department_id' => $faker->randomElement($departmentIds),
                'address' => $faker->address(),
            ]);
        }
    }
}
