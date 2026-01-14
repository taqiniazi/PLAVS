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
        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('shelf_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            
            // Remove old shelf_location column as we now have proper relationships
            $table->dropColumn('shelf_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('shelf_location')->after('publish_date');
            $table->dropForeign(['shelf_id']);
            $table->dropForeign(['category_id']);
            $table->dropColumn(['shelf_id', 'category_id']);
        });
    }
};
