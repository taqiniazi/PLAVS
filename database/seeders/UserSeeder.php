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
        $super = User::where('email', 'superadmin@superadmin.com')
            ->orWhere('username', 'superadmin')
            ->first();

        if ($super) {
            $super->update([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@superadmin.com',
                'password' => Hash::make('Taqi@Raza@Khan@1472'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]);
        } else {
            User::create([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@superadmin.com',
                'password' => Hash::make('Taqi@Raza@Khan@1472'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]);
        }
    }
}
