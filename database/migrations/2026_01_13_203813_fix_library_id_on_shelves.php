<?php

use App\Models\Shelf;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $shelves = Shelf::with('room')->whereNull('library_id')->get();
        foreach ($shelves as $shelf) {
            if ($shelf->room) {
                $shelf->library_id = $shelf->room->library_id;
                $shelf->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse
    }
};
