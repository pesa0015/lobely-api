<?php

use Illuminate\Database\Seeder;
use App\Book;
use App\Author;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $books = json_decode(file_get_contents(database_path() . '/seeds/Data/Books.json'));
        
        foreach ($books->books as $book) {
            $newBook = Book::create([
                'title' => $book->title,
                'original_title' => $book->original_title,
                'slug' => $book->slug,
                'publisher' => (isset($book->publisher)) ? $book->publisher : null,
                'published' => $book->published,
                'pages' => $book->pages,
                'cover' => $book->cover
            ]);

            foreach ($book->authors as $author) {
                $author = Author::where('name', $author->name)->first();

                $newBook->authors()->attach($author->id);
            }
        }
    }
}
