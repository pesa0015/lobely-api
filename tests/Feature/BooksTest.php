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
                'cover',
                'liked',
                'comment'
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

        $user->user->books()->attach($book, ['comment' => 'test']);

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
            'liked' => true,
            'comment' => 'test'
        ]);
    }

    /**
     * @group showUsersThatLikedBook
     *
     */
    public function testShowUsersThatLikedBook()
    {
        $user    = $this->newUser(true);

        $book    = factory(Book::class)->create([
            'title' => 'Test book',
            'slug'  => 'test-book'
        ]);

        $user->user->books()->attach($book, ['comment' => 'test']);

        $usersWithComments    = factory(\App\User::class, 8)->create()->each(function ($user) use ($book) {
            $user->books()->attach($book, ['comment' => 'test comment']);
        });

        $usersWithoutComments = factory(\App\User::class, 2)->create()->each(function ($user) use ($book) {
            $user->books()->attach($book);
        });

        $response = $this->callHttpWithToken('GET', 'books/' . $book->slug . '?showUsers=1', $user->token);
        $response->assertStatus(200);

        for ($i = 0; $i < $usersWithComments->count(); $i++) {
            $response->assertJsonFragment([
                'slug' => $usersWithComments[$i]->slug,
                'name' => $usersWithComments[$i]->name,
                'birthDate' => $usersWithComments[$i]->birth_date,
                'like' => [
                    'comment' => $usersWithComments[$i]->books()->where('book_id', $book->id)->first()->pivot->comment
                ]
            ]);
        }

        for ($i = 0; $i < $usersWithoutComments->count(); $i++) {
            $response->assertJsonFragment([
                'slug' => $usersWithComments[$i]->slug,
                'name' => $usersWithoutComments[$i]->name,
                'birthDate' => $usersWithoutComments[$i]->birth_date,
                'like' => [
                    'comment' => null
                ]
            ]);
        }

        $allUsers = $usersWithComments->merge($usersWithoutComments);

        $this->assertEquals($allUsers->count(), count($response->getData()));
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

        $this->assertDatabaseMissing('bookshelves', [
            'book_id' => $book->id,
            'user_id' => $user->user->id,
            'created_at' => null,
            'updated_at' => null
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
     * @group updateBookComment
     *
     */
    public function testUpdateBookComment()
    {
        $user  = $this->newUser(true);

        $token = $user->token;

        $book  = factory(Book::class)->create();

        $user->user->books()->attach($book, ['comment' => 'test']);

        $this->assertDatabaseHas('bookshelves', [
            'book_id' => $book->id,
            'user_id' => $user->user->id,
            'comment' => 'test'
        ]);

        $payload = [
            'comment' => 'comment'
        ];

        $response = $this->callHttpWithToken('PUT', 'bookshelfs/' . $book->id, $token, $payload);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('bookshelves', [
            'book_id' => $book->id,
            'user_id' => $user->user->id,
            'comment' => 'test'
        ]);

        $this->assertDatabaseHas('bookshelves', [
            'book_id' => $book->id,
            'user_id' => $user->user->id,
            'comment' => 'comment'
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
