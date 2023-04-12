<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->realText(20),
        ];
    }
}
