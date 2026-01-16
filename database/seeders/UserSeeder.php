<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
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
                'password' => Hash::make('Taqi@Raza@Khan@1472'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );
        if (! $super->wasRecentlyCreated) {
            $super->update([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('Taqi@Raza@Khan@1472'),
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
        if (! $admin->wasRecentlyCreated) {
            $admin->update([
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        $owner = User::firstOrCreate(
            ['email' => 'owner@library.com'],
            [
                'name' => 'Owner User',
                'username' => 'owner',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]
        );
        if (! $owner->wasRecentlyCreated) {
            $owner->update([
                'name' => 'Owner User',
                'username' => 'owner',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]);
        }

        $librarian = User::firstOrCreate(
            ['email' => 'librarian@library.com'],
            [
                'name' => 'Librarian User',
                'username' => 'librarian',
                'password' => Hash::make('password'),
                'role' => 'librarian',
                'parent_owner_id' => $owner->id,
                'email_verified_at' => now(),
            ]
        );
        if (! $librarian->wasRecentlyCreated) {
            $librarian->update([
                'name' => 'Librarian User',
                'username' => 'librarian',
                'password' => Hash::make('password'),
                'role' => 'librarian',
                'parent_owner_id' => $owner->id,
                'email_verified_at' => now(),
            ]);
        }

        $public = User::firstOrCreate(
            ['email' => 'public@library.com'],
            [
                'name' => 'Public User',
                'username' => 'public_user',
                'password' => Hash::make('password'),
                'role' => 'public',
                'email_verified_at' => now(),
            ]
        );
        if (! $public->wasRecentlyCreated) {
            $public->update([
                'name' => 'Public User',
                'username' => 'public_user',
                'password' => Hash::make('password'),
                'role' => 'public',
                'email_verified_at' => now(),
            ]);
        }
    }
}
