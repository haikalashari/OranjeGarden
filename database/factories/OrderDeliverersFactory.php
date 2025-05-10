<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDelivererFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::inRandomOrder()->first()->id ?? Order::factory(),
            'user_id' => User::where('deliverer')->inRandomOrder()->first()->id ?? User::factory(),
            'delivery_batch' => $this->faker->numberBetween(0, 1),
            'delivery_photo' => $this->faker->imageUrl(640, 480, 'plants', true),
            'status' => $this->faker->randomElement(['Mengantar', 'Mengganti', 'Ambil Kembali']),
        ];
    }
}
