<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderTotalPrice;
use App\Models\Invoices;

class InvoicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $billingRecords = OrderTotalPrice::all();

        if ($billingRecords->isEmpty()) {
            $this->command->info('No order_total_price records found. Please seed that first.');
            return;
        }

        foreach ($billingRecords as $billing) {
            Invoices::create([
                'order_id' => $billing->order_id,
                'invoice_batch' => $billing->billing_batch,
                'invoice_pdf_path' => 'invoices/' . uniqid() . '.pdf',
            ]);
        }
    }
}
