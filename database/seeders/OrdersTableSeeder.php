<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderStatus;
use App\Models\StatusCategory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing customers and deliverers
        $customer = Customer::first();
        $deliverer = User::where('role', 'delivery')->first();

        if (!$customer || !$deliverer) {
            $this->command->info('No customer or deliverer found! Please seed those first.');
            return;
        }

        Order::create([
            'customer_id' => $customer->id,
            'order_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(30),
            'delivery_address' => '123 Main St, Anytown, AN 12345',
            'payment_status' => 'paid',
            'payment_proof' => 'payment_proofs/sample.jpg',
            'total_price' => 150.00,
        ]);

        // Add more sample orders if needed
        Order::factory()->count(5)->create();
    }
}
