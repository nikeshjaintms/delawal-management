<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Update existing roles or create new ones
        $roles = [
            [
                'id' => 1,
                'name' => 'Admin',
                'role_name' => 'Admin',
                'description' => 'Full access to all modules and features',
                'status' => 'active',
            ],
            [
                'name' => 'Manager',
                'role_name' => 'Manager',
                'description' => 'Manager role with elevated permissions',
                'status' => 'active',
            ],
            [
                'name' => 'Sales User',
                'role_name' => 'Sales User',
                'description' => 'Sales team member with access to customer and sales modules',
                'status' => 'active',
            ],
            [
                'name' => 'Accountant',
                'role_name' => 'Accountant',
                'description' => 'Accountant with access to financial modules',
                'status' => 'active',
            ],
            [
                'name' => 'Viewer',
                'role_name' => 'Viewer',
                'description' => 'Read-only access to selected modules',
                'status' => 'active',
            ],
        ];

        foreach ($roles as $roleData) {
            if (isset($roleData['id'])) {
                Role::updateOrCreate(
                    ['id' => $roleData['id']],
                    array_merge($roleData, ['updated_at' => $now, 'created_at' => $now])
                );
            } else {
                Role::updateOrCreate(
                    ['role_name' => $roleData['role_name']],
                    array_merge($roleData, ['updated_at' => $now, 'created_at' => $now])
                );
            }
        }

        // Assign ALL permissions to Admin role (id=1)
        $adminRole = Role::find(1);
        if ($adminRole) {
            $allPermissions = Permission::pluck('id')->toArray();
            $adminRole->permissions()->sync($allPermissions);
            $this->command->info('Admin role assigned all permissions.');
        }

        $this->command->info('Roles seeded successfully!');
    }
}
