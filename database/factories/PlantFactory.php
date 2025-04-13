<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plant>
 */
class PlantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
            $plantNames = [
                'Sansevieria', 
                'Monstera',
                'Fiddle Leaf Fig',
                'Snake Plant',
                'Peace Lily',
                'ZZ Plant',
                'Pothos',
                'Rubber Plant',
                'Bird of Paradise',
                'Philodendron'
            ];
    
            return [
                'name' => $this->faker->unique()->randomElement($plantNames),
                'photo' => 'plants/' . Str::slug($this->faker->word) . '.jpg',
                'stock' => $this->faker->numberBetween(0, 50),
                'price' => $this->faker->numberBetween(50000, 500000),
                'qr_code' => Str::uuid(),
            ];
    }
}
