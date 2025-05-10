<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Invoices;
use Illuminate\Database\Seeder;

class InvoicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $billingRecords = Order::all();

        if ($billingRecords->isEmpty()) {
            $this->command->info('No total_price records found. Please seed that first.');
            return;
        }

        foreach ($billingRecords as $billing) {
            Invoices::create([
                'order_id' => $billing->id,
                'invoice_number' => 'INV-' . str_pad($billing->id, 6, '0', STR_PAD_LEFT),
                'invoice_pdf_path' => 'invoices/' . uniqid() . '.pdf',
            ]);
        }
    }
}
