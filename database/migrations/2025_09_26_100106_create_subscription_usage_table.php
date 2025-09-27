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
        Schema::create('subscription_usage', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subscription_id');
            $table->string('feature_type'); // 'products', 'transactions', 'users', 'outlets'
            $table->integer('current_usage')->default(0);
            $table->integer('annual_quota')->nullable(); // For transaction limits
            $table->date('subscription_year_start');
            $table->date('subscription_year_end');
            $table->boolean('soft_cap_triggered')->default(false);
            $table->timestamp('soft_cap_triggered_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            
            // Indexes
            $table->unique(['subscription_id', 'feature_type']);
            $table->index('soft_cap_triggered');
            $table->index('subscription_year_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_usage');
    }
};
