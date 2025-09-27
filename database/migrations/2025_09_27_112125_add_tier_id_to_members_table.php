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
        Schema::table('members', function (Blueprint $table) {
            $table->uuid('tier_id')->nullable()->after('last_visit_at');
            $table->index('tier_id');
            
            // Add foreign key constraint
            $table->foreign('tier_id')->references('id')->on('member_tiers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['tier_id']);
            $table->dropIndex(['tier_id']);
            $table->dropColumn('tier_id');
        });
    }
};
