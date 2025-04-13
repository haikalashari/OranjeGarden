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
            'email' => $this->faker->unique()->safeEmail,
            'total_orders' => 0,
            'total_spent' => 0,
        ];
    }

    // Optional state methods for specific scenarios
    public function withOrders()
    {
        return $this->state(function (array $attributes) {
            return [
                'total_orders' => $this->faker->numberBetween(1, 10),
                'total_spent' => $this->faker->numberBetween(100000, 1000000),
            ];
        });
    }
}
