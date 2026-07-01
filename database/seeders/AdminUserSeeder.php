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
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'phone' => '+244 927 476 913',
        ]);/*
        User::create([
            'name' => 'Secretária',
            'email' => 'secretaria@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'secretaria',
            'phone' => '+244 927 476 913',
        ]);
        User::create([
            'name' => 'Financeiro',
            'email' => 'financeiro@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'financeiro',
            'phone' => '+244 927 476 913',
        ]);*/

    }
}