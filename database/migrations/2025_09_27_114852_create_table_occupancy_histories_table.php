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
        Schema::create('table_occupancy_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id')->index();
            $table->uuid('table_id')->index();
            $table->uuid('order_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->index(); // Staff who processed the occupancy
            $table->timestamp('occupied_at');
            $table->timestamp('cleared_at')->nullable();
            $table->integer('duration_minutes')->nullable(); // Calculated when cleared
            $table->integer('party_size')->nullable(); // Number of guests
            $table->decimal('order_total', 12, 2)->nullable(); // Total order amount
            $table->enum('status', ['occupied', 'cleared', 'abandoned'])->default('occupied');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            // Indexes
            $table->index(['store_id', 'occupied_at']);
            $table->index(['table_id', 'occupied_at']);
            $table->index('status');
            
            // Foreign keys
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('tables')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_occupancy_histories');
    }
};
