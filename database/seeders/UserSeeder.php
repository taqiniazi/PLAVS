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
    }
}
