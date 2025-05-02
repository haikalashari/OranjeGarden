<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTotalPrice;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderTotalPricesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            // Ambil semua item terkait dengan order ini
            $orderItems = OrderItem::where('order_id', $order->id)->get();

            // Hitung total price berdasarkan jumlah item dan harga tanaman
            $totalPrice = $orderItems->sum(function ($item) {
                return $item->quantity * $item->plant->price;
            });

            // Update total_price pada order
            OrderTotalPrice::Create([
                'order_id' => $order->id,
                'billing_batch' => 0,
                'total_price' => $totalPrice,
            ]);
        }
    }
}


