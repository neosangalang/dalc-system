<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Check if an admin already exists so we don't accidentally make two!
        if (User::where('role', 'admin')->count() == 0) {
            User::create([
                'name'      => 'System Administrator',
                'username'  => 'admin', // The username you will use to log in
                'email'     => 'admin@dalc.edu',
                'password'  => Hash::make('admin1234'), // Your secure master password
                'role'      => 'admin',
                'is_active' => true,
            ]);
        }
    }
}