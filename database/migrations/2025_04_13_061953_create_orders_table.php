<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->date('order_date');
            $table->integer('rental_duration');
            $table->text('delivery_address');
            $table->decimal('total_price', 12, 2);
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->string('payment_proof')->nullable();
            $table->string('delivery_photo')->nullable();
            $table->foreignId('assigned_deliverer_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
