<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inspecao>
 */
class InspecaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Usa os nomes do seu fillable em Inspecao.php
        return [
            // Gera uma data aleatória no último ano
            'data_inspecao' => fake()->dateTimeBetween('-1 year', 'now'),

            'viu_rainha' => fake()->boolean(80), // 80% de chance de ser 'true'
            'nivel_populacao' => fake()->numberBetween(1, 5),
            'reservas_mel' => fake()->numberBetween(1, 5),
            'sinais_parasitas' => fake()->boolean(15), // 15% de chance de ter parasitas
            'observacoes' => fake()->sentence(),
            // O colmeia_id será preenchido automaticamente
        ];
    }
}
