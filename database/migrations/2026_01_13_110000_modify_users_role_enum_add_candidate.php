<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL ENUM needs raw SQL to modify allowed values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','librarian','owner','public','candidate') DEFAULT 'public'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous set (will convert any non-matching values to default)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','librarian','owner','student','teacher') DEFAULT 'student'");
    }
};
