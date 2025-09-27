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
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            $table->uuid('recipe_id');
            $table->foreignId('ingredient_product_id')->constrained('products'); // References products table for ingredients
            $table->decimal('quantity', 10, 3); // Quantity needed
            $table->string('unit'); // kg, liter, piece, etc.
            $table->decimal('unit_cost', 10, 2); // Cost per unit at time of recipe creation
            $table->decimal('total_cost', 10, 2); // quantity * unit_cost
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');

            // Indexes for performance
            $table->index(['store_id', 'recipe_id']);
            $table->index('ingredient_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
    }
};
