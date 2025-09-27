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
        Schema::table('tables', function (Blueprint $table) {
            $table->timestamp('occupied_at')->nullable()->after('is_active');
            $table->timestamp('last_cleared_at')->nullable()->after('occupied_at');
            $table->uuid('current_order_id')->nullable()->after('last_cleared_at');
            $table->integer('total_occupancy_count')->default(0)->after('current_order_id');
            $table->decimal('average_occupancy_duration', 8, 2)->default(0)->after('total_occupancy_count'); // in minutes
            $table->text('notes')->nullable()->after('average_occupancy_duration');
            
            // Add indexes
            $table->index('occupied_at');
            $table->index('current_order_id');
            
            // Add foreign key for current order
            $table->foreign('current_order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['current_order_id']);
            $table->dropIndex(['occupied_at']);
            $table->dropIndex(['current_order_id']);
            $table->dropColumn([
                'occupied_at',
                'last_cleared_at',
                'current_order_id',
                'total_occupancy_count',
                'average_occupancy_duration',
                'notes'
            ]);
        });
    }
};
