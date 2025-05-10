<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\StatusCategory;

class OrderStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();

        $waitingPaymentStatus = StatusCategory::where('status', 'Proses Pengantaran')->first();

        foreach ($orders as $order) {
            OrderStatus::create([
                'order_id' => $order->id,
                'status_id' => $waitingPaymentStatus->id,
                'created_at' => now(),
            ]);
        }
    }
}
