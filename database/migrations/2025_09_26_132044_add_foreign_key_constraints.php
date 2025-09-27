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
        // Add foreign key constraints
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('set null');
            $table->foreign('table_id')->references('id')->on('tables')->onDelete('set null');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
        });

        Schema::table('cash_sessions', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('cash_session_id')->references('id')->on('cash_sessions')->onDelete('set null');
        });

        // subscriptions and subscription_usage already have foreign keys defined
        // product_options already has foreign keys defined
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraints in reverse order
        // subscriptions, subscription_usage, and product_options foreign keys are handled in their own migrations

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['cash_session_id']);
        });

        Schema::table('cash_sessions', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['order_id']);
            $table->dropForeign(['payment_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['order_id']);
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['order_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['member_id']);
            $table->dropForeign(['table_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });
    }
};
