<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Literature', 'slug' => 'literature'],
            ['name' => 'Horror', 'slug' => 'horror'],
            ['name' => 'History', 'slug' => 'history'],
            ['name' => 'Poetry', 'slug' => 'poetry'],
            ['name' => 'Science', 'slug' => 'science'],
            ['name' => 'Fantasy', 'slug' => 'fantasy'],
            ['name' => 'Adventure', 'slug' => 'adventure'],
            ['name' => 'Self Development', 'slug' => 'self_development'],
            ['name' => 'Business', 'slug' => 'business'],
            ['name' => 'Marketing', 'slug' => 'marketing'],
            ['name' => 'Finance', 'slug' => 'finance'],
            ['name' => 'Programming', 'slug' => 'programming'],
            ['name' => 'Data Science', 'slug' => 'data_science'],
            ['name' => 'Crime', 'slug' => 'crime'],
            ['name' => 'Physics', 'slug' => 'physics'],
            ['name' => 'Chemistry', 'slug' => 'chemistry'],
            ['name' => 'Biology', 'slug' => 'biology'],
            ['name' => 'Astronomy', 'slug' => 'astronomy'],
            ['name' => 'Education', 'slug' => 'education'],
            ['name' => 'Art', 'slug' => 'art'],
            ['name' => 'Cooking', 'slug' => 'cooking'],
            ['name' => 'Health', 'slug' => 'health'],
            ['name' => 'Children', 'slug' => 'children'],
            ['name' => 'Psychology', 'slug' => 'psychology'],
            ['name' => 'Personal Growth', 'slug' => 'personal_growth'],
            ['name' => 'Philosophy', 'slug' => 'philosophy'],
            ['name' => 'Religion', 'slug' => 'religion'],
            ['name' => 'Politics', 'slug' => 'politics'],
            ['name' => 'Economics', 'slug' => 'economics'],
            ['name' => 'Sociology', 'slug' => 'sociology'],
            ['name' => 'Memoirs', 'slug' => 'memoirs'],
            ['name' => 'Short Stories', 'slug' => 'short_stories'],
            ['name' => 'Romance', 'slug' => 'romance'],
            ['name' => 'Science Fiction', 'slug' => 'science_fiction'],
            ['name' => 'Translated Books', 'slug' => 'translated_books'],
            ['name' => 'Geography', 'slug' => 'geography'],
            ['name' => 'Technology', 'slug' => 'technology'],
            ['name' => 'Cybersecurity', 'slug' => 'cybersecurity'],
            ['name' => 'Artificial Intelligence', 'slug' => 'artificial_intelligence'],
            ['name' => 'Mathematics', 'slug' => 'mathematics'],
            ['name' => 'Statistics', 'slug' => 'statistics'],
            ['name' => 'Academic Books', 'slug' => 'academic_books'],
            ['name' => 'Comics', 'slug' => 'comics'],
            ['name' => 'Sports', 'slug' => 'sports'],
            ['name' => 'Parenting', 'slug' => 'parenting'],
            ['name' => 'Family Relationships', 'slug' => 'family_relationships'],
        ];
        foreach($categories as $category)
        Category::create($category);
    }
}
