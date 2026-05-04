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
        $books = [
            ['book_name' => 'Book 1', 'description' => 'Book 1 is a simple and engaging book perfect for quick reading and learning new ideas.',
            'language' => 'arabic', 'number_of_pages' => '200',
            'selling_price' => '300', 'rental_price' => '50',
            'book_file' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
            'cover_image' => 'https://picsum.photos/300/400?random=1',
            'book_images' => ['https://picsum.photos/300/400?random=2',
             'https://picsum.photos/300/400?random=3',
             'https://picsum.photos/300/400?random=4',
             'https://picsum.photos/300/400?random=5',
             'https://picsum.photos/300/400?random=6',]],

            ['book_name' => 'Book 2', 'description' => 'Book 2 is a simple and engaging book perfect for quick reading and learning new ideas.',
            'language' => 'english', 'number_of_pages' => '500',
            'selling_price' => '400', 'rental_price' => '65.6',
            'book_file' => 'https://www.africau.edu/images/default/sample.pdf',
            'cover_image' => 'https://picsum.photos/300/200?random=7',
            'book_images' => ['https://picsum.photos/300/200?random=8',
             'https://picsum.photos/300/200?random=9',
             'https://picsum.photos/300/200?random=10',
             'https://picsum.photos/300/200?random=11',
             'https://picsum.photos/300/200?random=12']],

            ['book_name' => 'Book 3', 'description' => 'Book 3 is a simple and engaging book perfect for quick reading and learning new ideas.',
            'language' => 'german', 'number_of_pages' => '1700',
            'selling_price' => '3000', 'rental_price' => '300',
            'book_file' => 'https://www.orimi.com/pdf-test.pdf',
            'cover_image' => 'https://picsum.photos/300/400?random=1',
            'book_images' => ['https://picsum.photos/300/200?random=14',
             'https://picsum.photos/300/200?random=15',
             'https://picsum.photos/300/200?random=16',
             'https://picsum.photos/300/200?random=17',
             'https://picsum.photos/300/200?random=18',]]

        ];
        foreach ($books as $book)
        Book::create($book);
    }
}
