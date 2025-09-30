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
        Schema::create('sync_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('idempotency_key')->unique();
            $table->string('sync_type'); // 'order', 'inventory', 'payment', etc.
            $table->string('operation'); // 'create', 'update', 'delete'
            $table->string('entity_type'); // Model class name
            $table->uuid('entity_id')->nullable(); // ID of the synced entity
            $table->json('payload'); // Original sync data
            $table->json('conflicts')->nullable(); // Conflict details if any
            $table->string('status'); // 'pending', 'processing', 'completed', 'failed', 'conflict'
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['store_id', 'sync_type', 'status']);
            $table->index(['idempotency_key']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_histories');
    }
};