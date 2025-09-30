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
        Schema::table('payments', function (Blueprint $table) {
            // Add gateway integration fields
            $table->string('gateway')->nullable()->after('payment_method'); // stripe, paypal, etc.
            $table->string('gateway_transaction_id')->nullable()->after('gateway'); // external transaction ID
            $table->uuid('payment_method_id')->nullable()->after('gateway_transaction_id');
            $table->uuid('invoice_id')->nullable()->after('payment_method_id');
            $table->decimal('gateway_fee', 8, 2)->default(0)->after('invoice_id'); // fees charged by gateway
            $table->json('gateway_response')->nullable()->after('gateway_fee'); // store gateway response data
            
            // Add foreign key constraints
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
            
            // Add indexes
            $table->index(['gateway', 'gateway_transaction_id']);
            $table->index('payment_method_id');
            $table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['invoice_id']);
            
            // Drop indexes
            $table->dropIndex(['gateway', 'gateway_transaction_id']);
            $table->dropIndex(['payment_method_id']);
            $table->dropIndex(['invoice_id']);
            
            // Drop columns
            $table->dropColumn([
                'gateway',
                'gateway_transaction_id',
                'payment_method_id',
                'invoice_id',
                'gateway_fee',
                'gateway_response'
            ]);
        });
    }
};