<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
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
            'rental_duration' => 7, 
            'delivery_address' => '123 Main St, Anytown, AN 12345',
            'total_price' => 450000.00,
            'payment_status' => 'paid',
            'payment_proof' => 'payment_proofs/sample.jpg',
            'assigned_deliverer_id' => $deliverer->id,
        ]);

        // Add more sample orders if needed
        Order::factory()->count(5)->create();
    }
}
