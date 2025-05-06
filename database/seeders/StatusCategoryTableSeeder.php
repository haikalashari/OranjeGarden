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
            'Proses Pengantaran',
            'Dalam Masa Sewa',
            'Proses Penggantian Tanaman',
            'Proses Pengambilan Kembali',
            'Order Selesai',
            'Order Dibatalkan',
        ];

        foreach ($statuses as $status) {
            StatusCategory::create(['status' => $status]);
        }
    }
}
