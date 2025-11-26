<?php

namespace Database\Seeders;

use App\Models\Guardian;
use Illuminate\Database\Seeder;

class GuardianSeeder extends Seeder
{
    public function run()
    {
        Guardian::create([
            'name' => 'João Silva',
            'email' => 'joao.silva@email.com',
            'phone' => '+258 84 123 4567',
            'address' => 'Av. 25 de Setembro, Maputo',
            'relationship' => 'Pai'
        ]);

        Guardian::create([
            'name' => 'Maria Santos',
            'email' => 'maria.santos@email.com',
            'phone' => '+258 85 234 5678',
            'address' => 'Rua da Sé, Matola',
            'relationship' => 'Mãe'
        ]);
    }
}