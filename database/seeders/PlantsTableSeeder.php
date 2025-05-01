<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Plant;


class PlantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plant::create([
            'name' => 'Sansevieria',
            'photo' => 'plants/sansevieria.jpg',
            'stock' => 10,
            'price' => 150000,
            'category' => 'kecil',
        ]);

        // Create multiple random plants using factory
        Plant::factory()->count(9)->create();
    }
}
