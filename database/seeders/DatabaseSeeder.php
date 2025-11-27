<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin
        User::create([
            'name' => 'Dhoni',
            'email' => 'Dhoni@gmail.com',
            'password' => Hash::make('161102'),
            'email_verified_at' => now(),
        ]);

        // Buat user demo
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('demo123'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('✓ User accounts created successfully!');
        $this->command->info('  Dhoni: Dhoni@gmail.com / 161102');
        $this->command->info('  Demo: demo@example.com / demo123');
    }
}
