<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Superadmin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
        ]);

        $admin->assignRole('admin');

        $employee = User::create([
            'name' => 'Employee',
            'username' => 'employee',
            'email' => 'employee@gmail.com',
            'password' => bcrypt('employee'),
        ]);

        $employee->assignRole('employee');
    }
}
