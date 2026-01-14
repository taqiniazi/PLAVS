<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        $books = \App\Models\Book::all();

        // Create some sample activity logs
        foreach ($users as $user) {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'type' => 'user_registered',
                'description' => $user->name . ' joined the library',
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        foreach ($books as $book) {
            \App\Models\ActivityLog::create([
                'user_id' => $users->random()->id,
                'type' => 'book_added',
                'description' => 'New book added: ' . $book->title,
                'subject_type' => 'App\Models\Book',
                'subject_id' => $book->id,
                'created_at' => now()->subDays(rand(1, 15)),
            ]);
        }

        // Add some event activities
        for ($i = 0; $i < 5; $i++) {
            \App\Models\ActivityLog::create([
                'user_id' => $users->random()->id,
                'type' => 'event_created',
                'description' => 'New event created: Library Meeting #' . ($i + 1),
                'created_at' => now()->subDays(rand(1, 10)),
            ]);
        }
    }
}
