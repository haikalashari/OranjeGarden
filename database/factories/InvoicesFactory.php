<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class InvoicesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::inRandomOrder()->first()->id ?? Order::factory(),
            'batch_number' => $this->faker->numberBetween(1, 3),
            'invoice_pdf_path' => 'invoices/' . $this->faker->uuid . '.pdf',
        ];
    }
}
