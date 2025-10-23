<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Apiario;
use App\Models\Colmeia;
use App\Models\Inspecao;

class PopulacaoGeralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crie um Usuário de Teste
        $user = User::factory()->create([
            'name' => 'Apicultor Teste',
            'email' => 'teste@exemplo.com',
            'password' => bcrypt('123456'), // Senha: 123456
        ]);

        // 2. Crie 4 Apiários para este usuário
        Apiario::factory()
            ->count(4)
            ->for($user)
            ->has(
                // 3. Crie 5 Colmeias PARA CADA Apiário
                Colmeia::factory()
                    ->count(5)
                    ->has(
                        // 4. Crie 20 Inspeções PARA CADA Colmeia
                        Inspecao::factory()->count(20), 'inspecoes' // <-- CORRIGIDO
                    )
            )
            ->create();
    }
}
