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
        Schema::table('events', function (Blueprint $table) {
            $table->string('location')->nullable()->after('description');
            $table->string('speakers')->nullable()->after('location');
            $table->decimal('fee_amount', 10, 2)->nullable()->after('color');
            $table->string('fee_currency', 10)->nullable()->after('fee_amount');
            $table->string('bank_name')->nullable()->after('fee_currency');
            $table->string('bank_account')->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'speakers',
                'fee_amount',
                'fee_currency',
                'bank_name',
                'bank_account',
            ]);
        });
    }
};

