<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Invoices;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InvoicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Order::all()->each(function ($order) {
            $batchCount = rand(1, 3); 

            for ($i = 1; $i <= $batchCount; $i++) {
                Invoices::create([
                    'order_id' => $order->id,
                    'batch_number' => $i,
                    'invoice_pdf_path' => 'invoices/' . uniqid() . '.pdf',
                ]);
            }
        });
    }
}
