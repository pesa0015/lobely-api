<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Book;

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
}
