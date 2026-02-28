<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@booking.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '+33600000001',
        ]);

        // Manager
        User::create([
            'name'     => 'Manager',
            'email'    => 'manager@booking.com',
            'password' => Hash::make('password'),
            'role'     => 'manager',
            'phone'    => '+33600000002',
        ]);

        // Client
        User::create([
            'name'     => 'Client Test',
            'email'    => 'client@booking.com',
            'password' => Hash::make('password'),
            'role'     => 'client',
            'phone'    => '+33600000003',
        ]);
    }
}
