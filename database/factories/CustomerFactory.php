<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'contact_no' => '08' . $this->faker->numerify('########'),
            'secondary_contact_no' => $this->faker->optional()->numerify('08########'),
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
