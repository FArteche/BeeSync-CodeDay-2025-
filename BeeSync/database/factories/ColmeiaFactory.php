<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Colmeia>
 */
class ColmeiaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Usa 'identificacao' do seu fillable em Colmeia.php
            'identificacao' => 'Caixa #' . fake()->unique()->numberBetween(1, 1000),
            // O apiario_id será preenchido automaticamente
        ];
    }
}
