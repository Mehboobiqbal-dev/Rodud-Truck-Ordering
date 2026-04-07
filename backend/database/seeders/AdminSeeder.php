<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@rodud.com'],
            [
                'name' => 'Rodud Admin',
                'email' => 'admin@rodud.com',
                'password' => Hash::make('admin123'),
                'phone' => '+966500000000',
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
