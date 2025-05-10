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
        $orders = Order::all();
        $plants = Plant::all();

        if ($orders->isEmpty() || $plants->isEmpty()) {
            $this->command->info('No orders or plants found! Please seed those first.');
            return;
        }

        foreach ($orders as $order) {
            // Tambahkan batch awal (replacement_batch = 0)
            foreach ($plants->random(2) as $plant) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'plant_id' => $plant->id,
                    'quantity' => rand(1, 3),
                    'replacement_batch' => 0,
                ]);
            }

            // Tambahkan 1 atau 2 pergantian batch (replacement_batch = 1, 2, dst.)
            $batchCount = rand(1, 2);
            for ($batch = 1; $batch <= $batchCount; $batch++) {
                foreach ($plants->random(2) as $plant) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'plant_id' => $plant->id,
                        'quantity' => rand(1, 3),
                        'replacement_batch' => $batch,
                    ]);
                }
            }
        }
    }
    
}
