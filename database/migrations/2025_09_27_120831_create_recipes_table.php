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
        Schema::create('recipes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            $table->foreignId('product_id')->constrained('products');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('yield_quantity', 10, 2)->default(1); // How many units this recipe produces
            $table->string('yield_unit')->default('piece'); // unit, kg, liter, etc.
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('cost_per_unit', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

            // Indexes for performance
            $table->index(['store_id', 'product_id']);
            $table->index(['store_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
