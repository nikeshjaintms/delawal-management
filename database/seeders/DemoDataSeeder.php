<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Firm;
use App\Models\User;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create a demo firm
        $firm = Firm::create([
            'firm_name' => 'Delawala Properties',
            'email'     => 'admin@delawala.com',
            'mobile'    => '9999999999',
            'address'   => 'Mumbai, India',
            'city'      => 'Mumbai',
            'gst_no'    => null,
            'status'    => 'active',
            'password'  => bcrypt('password'),
        ]);

        // Create admin user
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@delawala.com',
            'password' => bcrypt('password'),
            'firm_id'  => $firm->id,
            'role'     => 'admin',
        ]);

        echo "✓ Demo data created successfully.\n";
        echo "  Login: admin@delawala.com\n";
        echo "  Password: password\n";
    }
}
