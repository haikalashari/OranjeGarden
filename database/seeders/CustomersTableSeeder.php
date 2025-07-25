<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;


class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create specific customer
        Customer::create([
            'name' => 'John Doe',
            'contact_no' => '08123456789',
            'secondary_contact_no' => '08987654321',
            'email' => 'johndoe@example.com',
        ]);

        // Create multiple random customers
        Customer::factory()
            ->count(10)
            ->create();

        // Create customers with orders history
        Customer::factory()
            ->count(5)
            ->create();
    } 
}
