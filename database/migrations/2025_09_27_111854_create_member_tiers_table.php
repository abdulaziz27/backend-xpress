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
        Schema::create('member_tiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id')->index();
            $table->string('name'); // Bronze, Silver, Gold, Platinum
            $table->string('slug')->unique(); // bronze, silver, gold, platinum
            $table->integer('min_points')->default(0);
            $table->integer('max_points')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0); // 0.00 to 100.00
            $table->json('benefits')->nullable(); // JSON array of benefits
            $table->string('color')->default('#6B7280'); // Hex color for UI
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['store_id', 'is_active']);
            $table->index('min_points');
            $table->unique(['store_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_tiers');
    }
};
