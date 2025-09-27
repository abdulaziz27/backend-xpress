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
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('current_stock')->default(0);
            $table->integer('reserved_stock')->default(0);
            $table->integer('available_stock')->default(0);
            $table->decimal('average_cost', 10, 2)->default(0);
            $table->decimal('total_value', 10, 2)->default(0);
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

            // Unique constraint - one stock level per product per store
            $table->unique(['store_id', 'product_id']);

            // Indexes for performance
            $table->index(['store_id', 'current_stock']);
            $table->index(['store_id', 'available_stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_levels');
    }
};
