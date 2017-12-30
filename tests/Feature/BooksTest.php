<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Book;
use App\Bookshelf;
use App\Author;

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
     * @group indexBooks
     *
     */
    public function testIndexBooks()
    {
        $token    = $this->newUser(true)->token;

        $book  = factory(Book::class)->create();
        $bookshelf = factory(Bookshelf::class)->create();

        $response = $this->callHttpWithToken('GET', 'bookshelfs', $token);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'originalTitle',
                'slug',
                'cover'
            ]
        ]);
    }

    /**
     * @group showBook
     *
     */
    public function testShowBook()
    {
        $user    = $this->newUser(true);

        $book    = factory(Book::class)->create([
            'title' => 'Test book',
            'slug'  => 'test-book'
        ]);

        $user->user->books()->attach($book);

        $author1 = factory(Author::class)->create();
        $author2 = factory(Author::class)->create();

        $book->authors()->save($author1);
        $book->authors()->save($author2);

        $response = $this->callHttpWithToken('GET', 'books/' . $book->slug, $user->token);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id'    => $book->id,
            'title' => $book->title,
            'slug'  => $book->slug,
            'authors' => [
                'data' => [
                    0 => [
                        'name' => $author1->name
                    ],
                    1 => [
                        'name' => $author2->name
                    ]
                ]
            ],
            'liked' => true
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
     * @group storeBookshelf
     *
     */
    public function testCantStoreBookshelfIfAlreadyLikedBook()
    {
        $user  = $this->newUser(true);

        $token = $user->token;

        $book  = factory(Book::class)->create();
        $bookshelf = factory(Bookshelf::class)->create();

        $payload = [
            'bookId' => $book->id
        ];

        $response = $this->callHttpWithToken('POST', 'bookshelfs', $token, $payload);
        $response->assertStatus(403);
        $response->assertJson([
            'already_liked_book' => true
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
