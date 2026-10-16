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

            $password = env('ADMIN_DEFAULT_PASSWORD', 'ChangeMe@2026');

            User::create([
                'name'      => 'System Administrator',
                'username'  => env('ADMIN_DEFAULT_USERNAME', 'admin'),
                'email'     => env('ADMIN_DEFAULT_EMAIL', 'admin@dalc.edu'),
                'password'  => Hash::make($password),
                'role'      => 'admin',
                'is_active' => true,
            ]);
        }
    }
}