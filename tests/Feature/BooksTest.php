<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Book;
use App\Bookshelf;

class BooksTest extends TestCase
{
    /**
     * @group searchBooks
     *
     */
    public function testSearchBooks()
    {
        $token    = $this->newUser(true)->token;

        $books    = factory(Book::class, 100)->create();
        $book1    = factory(Book::class)->create([
            'title' => 'Test book'
        ]);
        $book2    = factory(Book::class)->create([
            'title' => 'Book for test'
        ]);

        $response = $this->callHttpWithToken('GET', 'books', $token, ['title' => 'test']);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $book1->id,
            'title' => $book1->title,
            'slug' => $book1->slug
        ]);
        $response->assertJsonFragment([
            'id' => $book2->id,
            'title' => $book2->title,
            'slug' => $book2->slug
        ]);
    }

    /**
     * @group showBook
     *
     */
    public function testShowBook()
    {
        $token    = $this->newUser(true)->token;

        $book    = factory(Book::class)->create([
            'title' => 'Test book',
            'slug'  => 'test-book'
        ]);

        $response = $this->callHttpWithToken('GET', 'books/' . $book->slug, $token);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id'    => $book->id,
            'title' => $book->title,
            'slug'  => $book->slug
        ]);
    }

    /**
     * @group storeBookshelf
     *
     */
    public function testStoreBookshelf()
    {
        $user  = $this->newUser(true);

        $token = $user->token;

        $book  = factory(Book::class)->create();

        $payload = [
            'bookId' => $book->id
        ];

        $response = $this->callHttpWithToken('POST', 'bookshelfs', $token, $payload);
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('bookshelves', [
            'book_id' => $book->id,
            'user_id' => $user->user->id
        ]);
    }

    /**
     * @group deleteFromBookshelf
     *
     */
    public function testDeleteFromBookshelf()
    {
        $user  = $this->newUser(true);

        $token = $user->token;

        $book  = factory(Book::class)->create();
        $bookshelf = factory(Bookshelf::class)->create();

        $payload = [
            'bookId' => $book->id
        ];

        $response = $this->callHttpWithToken('DELETE', 'bookshelfs/' . $book->id, $token, $payload);
        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('bookshelves', [
            'book_id' => $book->id,
            'user_id' => $user->user->id
        ]);
    }
}
