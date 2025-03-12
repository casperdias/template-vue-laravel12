<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make Super Admin
        $superAdmin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin@app.com',
            'password' => \Hash::make('password'),
            'role_id' => 1,
        ]);

        // email_verified_at
        $superAdmin->markEmailAsVerified();
    }
}
