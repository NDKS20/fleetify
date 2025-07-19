<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = [
            'Access',
            'Create',
            'Read',
            'Update',
            'Delete',
        ];

        $models = [
            // User & Hak Akses
            'User',
            'Role',

            'Department',
            'Employee',

            'Attendance',
            'Attendance History',
        ];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                Permission::create([
                    'title' => $action . ' ' . $model,
                    'name' => strtolower($action) . '-' . strtolower(str_replace(' ', '_', $model)),
                ]);
            }
        }

        // Assign all permissions to the Admin and Owner roles
        $admin = Role::findByName('admin');
        $employee = Role::findByName('employee');

        $permissions = Permission::all();

        $admin->syncPermissions($permissions);
        $employee->syncPermissions($permissions);
    }
}
