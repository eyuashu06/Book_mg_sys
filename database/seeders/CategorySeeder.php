<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Fiction', 'Non-Fiction', 'Science Fiction', 'Fantasy',
            'Mystery', 'Romance', 'Thriller', 'Biography',
            'History', 'Self-Help', 'Technology', 'Science',
            'Poetry', 'Drama', 'Comics', 'Children',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
