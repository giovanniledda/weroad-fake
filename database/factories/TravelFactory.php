<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use function now;
use function rand;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Travel>
 */
class TravelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'publicationDate' => now()->subDays(rand(1, 7)),
            'name' => fake()->country . ' ' . fake()->colorName,
            'description' => fake()->realText(250),
            'days' => fake()->numberBetween(1, 365),
        ];
    }

    /**
     * Indicate that the travel's publication date is present.
     *
     * @return static
     */
    public function public()
    {
        return $this->state(fn (array $attributes) => [
            'publicationDate' => now()->subDays(rand(1, 7)),
        ]);
    }

    /**
     * Indicate that the travel's publication date is null.
     *
     * @return static
     */
    public function private()
    {
        return $this->state(fn (array $attributes) => [
            'publicationDate' => null,
        ]);
    }
}
