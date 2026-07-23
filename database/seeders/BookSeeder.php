<?php

namespace Database\Seeders;

use App\Models\Book; 
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 books using the factory
        Book::factory(10)->create();

        // Or create specific books manually
        Book::create([
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'isbn' => '9780743273565',
            'published_year' => 1925,
            'description' => 'A story about the mysterious Jay Gatsby...'
        ]);

        Book::create([
            'title' => 'To Kill a Mockingbird',
            'author' => 'Harper Lee',
            'isbn' => '9780061120084',
            'published_year' => 1960,
            'description' => 'A classic of modern American literature...'
	]);
    }
}
