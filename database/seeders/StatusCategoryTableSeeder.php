<?php

namespace Database\Seeders;

use App\Models\StatusCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {
        $statuses = [
            'Menunggu Pembayaran',
            'Proses Pengantaran',
            'Sudah Diantar',
            'Dalam Masa Sewa',
            'Proses Pengambilan Kembali',
            'Order Selesai',
            'Order Dibatalkan',
        ];

        foreach ($statuses as $status) {
            StatusCategory::create(['status' => $status]);
        }
    }
}
