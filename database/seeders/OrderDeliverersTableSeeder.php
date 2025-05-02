<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderDeliverers;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderDeliverersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deliverers = User::where('role', 'delivery')->get();

        if ($deliverers->isEmpty()) {
            echo "No users with role 'deliverer' found. Seeder skipped.\n";
            return;
        }

        // Ambil semua order yang memiliki order_items
        $orders = Order::has('orderItems')->get();

        foreach ($orders as $order) {
            // Ambil semua batch unik dari order_items (replacement_batch)
            $batches = OrderItem::where('order_id', $order->id)
                ->pluck('replacement_batch')
                ->unique();

            foreach ($batches as $batch) {
                OrderDeliverers::create([
                    'order_id' => $order->id,
                    'user_id' => $deliverers->random()->id,
                    'delivery_batch' => $batch,
                    'delivery_photo' => 'deliveries/' . uniqid() . '.jpg',
                    'status' => $batch == 0 ? 'Mengantar' : 'Mengganti',
                ]);
            }
        }
    }
}
