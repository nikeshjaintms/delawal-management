<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Firm;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create firm
        $firm = Firm::first();
        if (!$firm) {
            $firm = Firm::create([
                'firm_name' => 'Delawala Properties',
                'email'     => 'admin@gmail.com',
                'mobile'    => '9999999999',
                'address'   => 'Mumbai, India',
                'city'      => 'Mumbai',
                'gst_no'    => null,
                'status'    => 'active',
                'password'  => Hash::make('123456'),
            ]);
        }

        // Delete existing admin@gmail.com if exists
        User::where('email', 'admin@gmail.com')->delete();

        // Create new admin user with specified credentials
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        $roleId = $adminRole ? $adminRole->id : 1;

        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'firm_id'  => $firm->id,
            'role_id'  => $roleId,
            'role'     => 'admin',
        ]);

        echo "✓ Admin user created successfully.\n";
        echo "  Email: admin@gmail.com\n";
        echo "  Password: 123456\n";
        echo "  Password Hash: " . Hash::make('123456') . "\n";
    }
}
