<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Column;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Column>
 */
class ColumnFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'order' => fake()->numberBetween(0, 10),
            'board_id' => Board::factory(),
        ];
    }
} 