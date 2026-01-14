<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super = User::firstOrCreate(
            ['email' => 'superadmin@admin.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );
        if (!$super->wasRecentlyCreated) {
            $super->update([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        if (!$admin->wasRecentlyCreated) {
            $admin->update([
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        $librarian = User::firstOrCreate(
            ['email' => 'librarian@admin.com'],
            [
                'name' => 'Taqi Raza Khan',
                'username' => 'librarian',
                'password' => Hash::make('password'),
                'role' => 'librarian',
                'email_verified_at' => now(),
            ]
        );
        if (!$librarian->wasRecentlyCreated) {
            $librarian->update([
                'name' => 'Taqi Raza Khan',
                'username' => 'librarian',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        $public = User::firstOrCreate(
            ['email' => 'public@admin.com'],
            [
                'name' => 'Ali Khan',
                'username' => 'ali_khan',
                'password' => Hash::make('password'),
                'role' => 'public',
                'email_verified_at' => now(),
            ]
        );
        if (!$public->wasRecentlyCreated) {
            $public->update([
                'name' => 'Ali Khan',
                'username' => 'ali_khan',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
