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
        Schema::table('book_user', function (Blueprint $table) {
            $table->timestamp('return_date')->nullable()->after('assigned_at');
            $table->boolean('is_returned')->default(false)->after('return_date');
            $table->text('return_notes')->nullable()->after('is_returned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_user', function (Blueprint $table) {
            $table->dropColumn(['return_date', 'is_returned', 'return_notes']);
        });
    }
};
