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
            'username' => 'superadmin',
            'email' => 'superadmin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        // Admin
        \App\Models\User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Librarian
        \App\Models\User::create([
            'name' => 'Taqi Raza Khan',
            'username' => 'librarian',
            'email' => 'librarian@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'librarian',
            'email_verified_at' => now(),
        ]);

        // Teacher
        \App\Models\User::create([
            'name' => 'Sarah Ahmed',
            'username' => 'sarah_ahmed',
            'email' => 'teacher@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'teacher',
            'email_verified_at' => now(),
        ]);

        // Student
        \App\Models\User::create([
            'name' => 'Ali Khan',
            'username' => 'ali_khan',
            'email' => 'student@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
    }
}
