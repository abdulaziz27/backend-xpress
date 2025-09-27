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
        Schema::create('cogs_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            $table->foreignId('product_id')->constrained('products');
            $table->uuid('order_id')->nullable();
            $table->integer('quantity_sold');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cogs', 10, 2);
            $table->enum('calculation_method', ['fifo', 'lifo', 'weighted_average']);
            $table->json('cost_breakdown')->nullable(); // For detailed FIFO/LIFO calculations
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');

            // Indexes for performance
            $table->index(['store_id', 'product_id']);
            $table->index(['store_id', 'created_at']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cogs_history');
    }
};
