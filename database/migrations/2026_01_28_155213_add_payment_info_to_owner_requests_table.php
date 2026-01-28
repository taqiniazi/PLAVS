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
        Schema::table('owner_requests', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('library_phone');
            $table->string('transaction_screenshot_path')->nullable()->after('payment_method');
            $table->decimal('amount', 10, 2)->default(1000.00)->after('transaction_screenshot_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owner_requests', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'transaction_screenshot_path', 'amount']);
        });
    }
};
