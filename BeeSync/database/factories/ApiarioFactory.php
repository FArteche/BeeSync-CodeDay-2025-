<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User; // Precisamos do User para a relação

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apiario>
 */
class ApiarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Usa os nomes do seu fillable em Apiario.php
            'nome' => 'Apiário ' . fake()->word(),
            'localizacao' => fake()->city(),
            // O user_id será preenchido automaticamente pela relação
        ];
    }
}
