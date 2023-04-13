<?php

namespace Database\Factories;

use App\Models\Travel;
use function fake;
use Illuminate\Database\Eloquent\Factories\Factory;
use function now;
use function rand;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->regexify('IT[A-Z]{3}[0-9]{8}'),
            'startingDate' => now()->addMonths(rand(1, 12))->format('Y-m-d'),
            'endingDate' => now()->addMonths(rand(3, 24))->format('Y-m-d'),
            'price' => fake()->randomNumber(5),
            'travelId' => Travel::factory(),
        ];
    }
}
