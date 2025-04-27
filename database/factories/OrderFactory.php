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
            'rental_duration' => $this->faker->numberBetween(1, 30),
            'delivery_address' => $this->faker->address,
            'total_price' => $this->faker->randomFloat(2, 100000, 1000000),
            'payment_status' => $this->faker->randomElement(['paid', 'unpaid']),
            'payment_proof' => $this->faker->optional()->imageUrl(),
            'delivery_photo' => $this->faker->optional()->imageUrl(),
            'assigned_deliverer_id' => User::where('role', 'delivery')->inRandomOrder()->first()->id,
        ];
    }
}
