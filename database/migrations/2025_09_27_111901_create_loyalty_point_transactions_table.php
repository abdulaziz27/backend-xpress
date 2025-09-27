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
        Schema::create('loyalty_point_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id')->index();
            $table->uuid('member_id')->index();
            $table->uuid('order_id')->nullable()->index(); // If related to an order
            $table->unsignedBigInteger('user_id')->index(); // Staff who processed the transaction
            $table->enum('type', ['earned', 'redeemed', 'adjusted', 'expired']);
            $table->integer('points'); // Positive for earned/adjusted up, negative for redeemed/adjusted down
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->string('reason')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional data like earning rules applied
            $table->timestamp('expires_at')->nullable(); // For point expiration
            $table->timestamps();
            
            // Indexes
            $table->index(['member_id', 'type']);
            $table->index(['store_id', 'created_at']);
            $table->index('expires_at');
            
            // Foreign keys
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_transactions');
    }
};
