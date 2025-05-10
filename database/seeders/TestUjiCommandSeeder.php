<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\StatusCategory;
use App\Models\OrderDeliverers;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestUjiCommandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeder untuk Order dan status uji
Order::create([
    'id' => 1,
    'customer_id' => 1,
    'order_date' => '2025-04-23',
    'end_date' => '2025-05-21',
]);
OrderStatus::create([
    'order_id' => 1,
    'status_id' => StatusCategory::where('status', 'Dalam Masa Sewa')->value('id'),
    'created_at' => now(),
]);

Order::create([
    'id' => 2,
    'customer_id' => 1,
    'order_date' => '2025-04-09',
    'end_date' => '2025-05-09',
]);
OrderStatus::create([
    'order_id' => 2,
    'status_id' => StatusCategory::where('status', 'Proses Penggantian Tanaman')->value('id'),
    'created_at' => now(),
]);
OrderDeliverers::create([
    'order_id' => 2,
    'status' => 'Proses Penggantian Tanaman',
    'delivery_photo' => null,
]);

Order::create([
    'id' => 3,
    'customer_id' => 1,
    'order_date' => '2025-04-07',
    'end_date' => '2025-05-07',
]);
OrderStatus::create([
    'order_id' => 3,
    'status_id' => StatusCategory::where('status', 'Dalam Masa Sewa')->value('id'),
    'created_at' => now(),
]);

Order::create([
    'id' => 4,
    'customer_id' => 1,
    'order_date' => '2025-04-07',
    'end_date' => '2025-05-17',
]);
OrderStatus::create([
    'order_id' => 4,
    'status_id' => StatusCategory::where('status', 'Proses Pengambilan Kembali')->value('id'),
    'created_at' => now(),
]);
OrderDeliverers::create([
    'order_id' => 4,
    'status' => 'Proses Pengambilan Kembali',
    'delivery_photo' => null,
]);

Order::create([
    'id' => 5,
    'customer_id' => 1,
    'order_date' => '2025-05-04',
    'end_date' => '2025-06-03',
]);
OrderStatus::create([
    'order_id' => 5,
    'status_id' => StatusCategory::where('status', 'Dalam Masa Sewa')->value('id'),
    'created_at' => now(),
]);

Order::create([
    'id' => 6,
    'customer_id' => 1,
    'order_date' => '2025-04-17',
    'end_date' => '2025-05-17',
]);
OrderStatus::create([
    'order_id' => 6,
    'status_id' => StatusCategory::where('status', 'Order Dibatalkan')->value('id'),
    'created_at' => now(),
]);
    }
}
