<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\Plant;
use App\Models\OrderItem;
use Database\factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = OrderItem::class;

    public function definition(): array
    {
        // Get or create an order to reuse
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();
        
        return [
            'order_id' => $order->id, // Use existing order ID
            'plant_id' => Plant::inRandomOrder()->first()->id,
            'quantity' => $this->faker->numberBetween(1, 10),
            'replacement_batch' => $this->faker->numberBetween(0, 1), // 50% chance of being true
        ];
    }

    /**
     * Configure to create multiple items for the same order
     */
    public function forExistingOrder(int $count = 3): self
    {
        return $this->state(function (array $attributes) use ($count) {
            static $order;
            
            if (!$order) {
                $order = Order::inRandomOrder()->first() ?? Order::factory()->create();
            }
            
            return [
                'order_id' => $order->id,
                // Create exactly $count items for this order
            ];
        })->count($count);
    }
}
