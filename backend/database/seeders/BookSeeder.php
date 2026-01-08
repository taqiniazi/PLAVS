<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Book::create([
            'title' => 'Strategic Procurement Management',
            'author' => 'Eric Verzuh',
            'isbn' => '978-0596006138',
            'edition' => '1st Edition',
            'publisher' => 'O\'Reilly Media',
            'publish_date' => '2020-01-15',
            'shelf_location' => 'Shelf A-1',
            'owner' => 'Taqi Raza',
            'description' => 'A comprehensive guide to strategic procurement management.',
            'visibility' => true,
            'status' => 'Available',
            'image' => 'book2.png'
        ]);

        \App\Models\Book::create([
            'title' => 'Making Things Happen',
            'author' => 'Scott Berkun',
            'isbn' => '978-0596007656',
            'edition' => '2nd Edition',
            'publisher' => 'O\'Reilly Media',
            'publish_date' => '2019-05-20',
            'shelf_location' => 'Shelf C-4',
            'owner' => 'Library Admin',
            'description' => 'A practical guide to project management.',
            'visibility' => false,
            'status' => 'Borrowed (Ali)',
            'image' => 'book1.png'
        ]);

        \App\Models\Book::create([
            'title' => 'Clean Code',
            'author' => 'Robert C. Martin',
            'isbn' => '978-0132350884',
            'edition' => '1st Edition',
            'publisher' => 'Prentice Hall',
            'publish_date' => '2008-08-01',
            'shelf_location' => 'Shelf B-2',
            'owner' => 'Taqi Raza',
            'description' => 'A handbook of agile software craftsmanship.',
            'visibility' => true,
            'status' => 'Available',
            'image' => 'book2.png'
        ]);
    }
}
