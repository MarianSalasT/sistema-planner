<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Column;
use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(2),
            'description' => fake()->paragraph(),
            'order' => fake()->numberBetween(0, 10),
            'column_id' => Column::factory(),
            'created_by' => User::factory(),
        ];
    }
} 