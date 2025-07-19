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

        for ($i = 0; $i < 20; $i++) {
            $employee = User::create([
                'name' => 'Employee ' . $i,
                'username' => 'employee' . $i,
                'email' => 'employee' . $i . '@gmail.com',
                'password' => bcrypt('employee'),
            ]);

            $employee->assignRole('employee');
        }
    }
}
