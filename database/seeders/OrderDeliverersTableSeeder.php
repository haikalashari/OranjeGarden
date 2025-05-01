<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
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

        // Ensure there is at least one deliverer
        if ($deliverers->isEmpty()) {
            echo "No users with role 'deliverer' found. Seeder skipped.\n";
            return;
        }

        Order::all()->each(function ($order) use ($deliverers) {
            $deliverer = $deliverers->random();

            OrderDeliverers::create([
                'order_id' => $order->id,
                'user_id' => $deliverer->id,
                'batch_number' => 1,
                'delivery_photo' => 'deliveries/' . uniqid() . '.jpg',
                'status' => 'Mengantar',
            ]);
        });    }
}
