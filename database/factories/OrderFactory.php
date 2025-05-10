<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'order_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'delivery_address' => $this->faker->address,
            'payment_status' => $this->faker->randomElement(['paid', 'unpaid']),
            'payment_proof' => $this->faker->optional()->imageUrl(),
            'total_price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
