<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Superadmin
        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        // Admin
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Librarian
        \App\Models\User::create([
            'name' => 'Taqi Raza Khan',
            'email' => 'librarian@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'librarian',
            'email_verified_at' => now(),
        ]);

        // Teacher
        \App\Models\User::create([
            'name' => 'Sarah Ahmed',
            'email' => 'teacher@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'teacher',
            'email_verified_at' => now(),
        ]);

        // Student
        \App\Models\User::create([
            'name' => 'Ali Khan',
            'email' => 'student@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
    }
}
