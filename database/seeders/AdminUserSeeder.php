<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@monatower.edu.mz',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '+244 927 476 913',
        ]);
    }
}