<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Firm;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin'], [
            'display_name' => 'Administrator',
            'description'  => 'Full access to all modules and features',
        ]);

        // Get or create primary firm
        $firm = Firm::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'firm_name' => 'Delawala Properties',
                'mobile'    => '9999999999',
                'address'   => 'Mumbai, India',
                'city'      => 'Mumbai',
                'gst_no'    => null,
                'status'    => 'active',
                'password'  => Hash::make('admin@123'),
            ]
        );
        $firm->update([
            'password' => Hash::make('admin@123'),
            'status'   => 'active',
        ]);

        // Ensure delawala firm also exists
        $delawalaFirm = Firm::firstOrCreate(
            ['email' => 'admin@delawala.com'],
            [
                'firm_name' => 'Delawala Group',
                'mobile'    => '9888888888',
                'address'   => 'Mumbai, India',
                'city'      => 'Mumbai',
                'gst_no'    => null,
                'status'    => 'active',
                'password'  => Hash::make('admin@123'),
            ]
        );
        $delawalaFirm->update([
            'password' => Hash::make('admin@123'),
            'status'   => 'active',
        ]);

        // Create or update admin@gmail.com user
        $user1 = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('admin@123'),
                'firm_id'  => $firm->id,
                'role_id'  => $adminRole->id,
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );

        // Create or update admin@delawala.com user
        $user2 = User::updateOrCreate(
            ['email' => 'admin@delawala.com'],
            [
                'name'     => 'Delawala Admin',
                'password' => Hash::make('admin@123'),
                'firm_id'  => $firm->id,
                'role_id'  => $adminRole->id,
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );

        echo "✓ Admin users & firms seeded successfully.\n";
        echo "  Email 1: admin@gmail.com | Password: admin@123\n";
        echo "  Email 2: admin@delawala.com | Password: admin@123\n";
    }
}

