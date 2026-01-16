<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Library;
use App\Models\Room;
use App\Models\Shelf;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::where('role', 'owner')->first()
            ?? User::where('role', 'admin')->first()
            ?? User::first();

        $ownerName = $owner ? $owner->name : 'Demo Owner';

        $library = Library::firstOrCreate(
            [
                'name' => 'Demo Central Library',
                'owner_id' => $owner?->id,
            ],
            [
                'type' => 'public',
                'location' => 'Demo City',
                'description' => 'Demo library used for seeded data.',
            ]
        );

        $managementRoom = Room::firstOrCreate(
            [
                'name' => 'Management',
                'library_id' => $library->id,
            ],
            [
                'description' => 'Management section room.',
                'floor' => '1',
            ]
        );

        $financeRoom = Room::firstOrCreate(
            [
                'name' => 'Finance',
                'library_id' => $library->id,
            ],
            [
                'description' => 'Finance section room.',
                'floor' => '1',
            ]
        );

        $managementShelf = Shelf::firstOrCreate(
            [
                'name' => 'MGT-01',
                'room_id' => $managementRoom->id,
            ],
            [
                'code' => 'MGT-01',
                'library_id' => $library->id,
                'description' => 'Management books shelf.',
            ]
        );

        $financeShelf = Shelf::firstOrCreate(
            [
                'name' => 'FIN-01',
                'room_id' => $financeRoom->id,
            ],
            [
                'code' => 'FIN-01',
                'library_id' => $library->id,
                'description' => 'Finance books shelf.',
            ]
        );

        $coverPath = 'uploads/books/demo_seed_cover.png';

        if (! Storage::disk('public')->exists($coverPath)) {
            $source = public_path('images/book1.png');
            if (file_exists($source)) {
                Storage::disk('public')->put($coverPath, file_get_contents($source));
            }
        }

        $books = [
            [
                'title' => 'The Pragmatic Programmer',
                'author' => 'Andrew Hunt, David Thomas',
                'isbn' => '9780201616224',
                'edition' => '1st',
                'publisher' => 'Addison-Wesley',
                'publish_date' => '1999-10-30',
                'shelf_location' => 'A-1',
                'shelf_id' => $managementShelf->id,
                'owner' => $ownerName,
                'description' => 'Classic book on pragmatic software development practices.',
                'visibility' => true,
                'status' => 'Available',
                'image' => $coverPath,
                'cover_image' => $coverPath,
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '9780132350884',
                'edition' => '1st',
                'publisher' => 'Prentice Hall',
                'publish_date' => '2008-08-11',
                'shelf_location' => 'A-2',
                'shelf_id' => $managementShelf->id,
                'owner' => $ownerName,
                'description' => 'Handbook of agile software craftsmanship.',
                'visibility' => true,
                'status' => 'Available',
                'image' => $coverPath,
                'cover_image' => $coverPath,
            ],
            [
                'title' => 'Design Patterns',
                'author' => 'Erich Gamma et al.',
                'isbn' => '9780201633610',
                'edition' => '1st',
                'publisher' => 'Addison-Wesley',
                'publish_date' => '1994-10-31',
                'shelf_location' => 'B-1',
                'shelf_id' => $financeShelf->id,
                'owner' => $ownerName,
                'description' => 'Elements of reusable object-oriented software.',
                'visibility' => true,
                'status' => 'Available',
                'image' => $coverPath,
                'cover_image' => $coverPath,
            ],
        ];

        foreach ($books as $data) {
            Book::updateOrCreate(
                [
                    'title' => $data['title'],
                    'author' => $data['author'],
                ],
                $data
            );
        }
    }
}
