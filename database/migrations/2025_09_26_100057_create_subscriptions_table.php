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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            $table->unsignedBigInteger('plan_id');
            $table->enum('status', ['active', 'inactive', 'cancelled', 'expired'])->default('active');
            $table->enum('billing_cycle', ['monthly', 'annual'])->default('monthly');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->date('trial_ends_at')->nullable();
            $table->decimal('amount', 10, 2);
            $table->json('metadata')->nullable(); // Store additional subscription data
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('restrict');
            
            // Indexes
            $table->index(['store_id', 'status']);
            $table->index('status');
            $table->index('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
