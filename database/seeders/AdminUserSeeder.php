<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => config('app.admin_email', 'admin@gewo-freital.de')],
            [
                'first_name' => config('app.admin_first_name', 'Admin'),
                'last_name' => config('app.admin_last_name', 'User'),
                'password' => Hash::make(config('app.admin_password', 'password')),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );
    }
}
