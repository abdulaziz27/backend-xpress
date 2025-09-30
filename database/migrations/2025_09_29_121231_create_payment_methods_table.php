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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('gateway'); // stripe, paypal, etc.
            $table->string('gateway_id'); // external payment method ID
            $table->enum('type', ['card', 'bank_account', 'digital_wallet', 'other'])->default('card');
            $table->string('last_four', 4)->nullable(); // for display purposes
            $table->date('expires_at')->nullable(); // for cards
            $table->boolean('is_default')->default(false);
            $table->json('metadata')->nullable(); // store additional payment method data
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['user_id', 'is_default']);
            $table->index(['gateway', 'gateway_id']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};