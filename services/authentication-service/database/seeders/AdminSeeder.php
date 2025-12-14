<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AuthCredential;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Check if admin already exists to prevent duplicates
        if (AuthCredential::where('email', 'admin@smarthome.com')->exists()) {
            $this->command->info('Admin user already exists.');
            return;
        }

        AuthCredential::create([
            'user_id' => 'ADMIN_MASTER_ID', // In a real app, this links to a User Profile
            'email' => 'admin@smarthome.com',
            'password' => Hash::make('admin123'), // Default secure password
            'role' => 'admin', // <--- THIS IS THE KEY PERMISSION FIELD
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@smarthome.com');
        $this->command->info('Password: admin123');
    }
}