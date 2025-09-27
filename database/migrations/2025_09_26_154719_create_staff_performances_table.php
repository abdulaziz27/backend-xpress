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
        Schema::create('staff_performances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->integer('orders_processed')->default(0);
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->integer('refunds_processed')->default(0);
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->integer('hours_worked')->default(0);
            $table->decimal('sales_per_hour', 10, 2)->default(0);
            $table->integer('customer_interactions')->default(0);
            $table->decimal('customer_satisfaction_score', 3, 2)->nullable(); // 0-5 scale
            $table->json('additional_metrics')->nullable(); // For custom metrics
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['store_id', 'user_id', 'date']);
            $table->index(['store_id', 'date']);
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_performances');
    }
};
