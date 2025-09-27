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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id')->index();
            $table->uuid('cash_session_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('category'); // office_supplies, utilities, marketing, etc.
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->string('receipt_number')->nullable();
            $table->string('vendor')->nullable();
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            
            // Indexes
            $table->index(['store_id', 'category']);
            $table->index(['store_id', 'expense_date']);
            $table->index('cash_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
