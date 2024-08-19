<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'area' => fake()->numberBetween(15, 100),
            'room_number' => fake()->numberBetween(1, 5),
            'address' => fake()->address(),
            'price' => fake()->randomFloat(0, 1000, 20000),
            'city_id' => City::all()->random()->id,
        ];
    }
}
