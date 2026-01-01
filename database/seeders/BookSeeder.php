<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $book = Book::create([
            'title' => 'サンプル漫画 第1巻',
            'author' => 'Laravel 太郎',
            'total_pages' => 10,
        ]);

        for ($i = 1; $i <= 10; $i++) {
            BookPage::create([
                'book_id' => $book->id,
                'page_number' => $i,
                // 実際には存在しないパスでも、DBのテストには十分です
                'file_path' => "books/{$book->id}/p".sprintf('%03d', $i).'.jpg',
            ]);
        }
    }
}
