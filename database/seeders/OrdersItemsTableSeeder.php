<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Plant;
use Database\Seeders\OrdersTableSeeder;
use Database\Seeders\PlantsTableSeeder;
use Database\Seeders\CustomersTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\OrdersItemsTableSeeder;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrdersItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = Order::first();
        $plant = Plant::first();

        if (!$order || !$plant) {
            $this->command->info('No orders or plants found! Please seed those first.');
            return;
        }

        OrderItem::create([
            'order_id' => $order->id,
            'plant_id' => $plant->id,
            'quantity' => 3,
        ]);

        // Add more sample items if needed
        OrderItem::factory()->count(10)->create();
    }
    
}
