<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\OrdersTableSeeder;
use Database\Seeders\PlantsTableSeeder;
use Database\Seeders\CustomersTableSeeder;
use Database\Seeders\OrdersItemsTableSeeder;
use Database\Seeders\OrderStatusTableSeeder;
use Database\Seeders\StatusCategoryTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            CustomersTableSeeder::class,
            PlantsTableSeeder::class,
            OrdersTableSeeder::class,   
            OrdersItemsTableSeeder::class,
            StatusCategoryTableSeeder::class,
            OrderStatusTableSeeder::class,
        ]);
    }
}
