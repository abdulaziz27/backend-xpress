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
        // For MySQL, we need to modify the enum column
        DB::statement("ALTER TABLE payment_methods MODIFY COLUMN type ENUM('card', 'bank_account', 'bank_transfer', 'digital_wallet', 'va', 'qris', 'other') DEFAULT 'card'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE payment_methods MODIFY COLUMN type ENUM('card', 'bank_account', 'digital_wallet', 'other') DEFAULT 'card'");
    }
};