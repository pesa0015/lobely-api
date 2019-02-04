<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Book;
use App\Bookshelf;
use App\User;

class HeartsTest extends TestCase
{
    /**
     * @group storeHeart
     *
     */
    public function testStoreHeart()
    {
        $me = $this->newUser(true);

        $token = $me->token;

        $book = factory(Book::class)->create();

        // Test post with missing payload
        $response = $this->callHttpWithToken('POST', 'hearts', $token);
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'userId' => ['The user id field is required.'],
                'bookId' => ['The book id field is required.']
            ]
        ]);

        $payload = [
            'bookId' => $book->id
        ];

        // Test post with missing userId payload
        $response = $this->callHttpWithToken('POST', 'hearts', $token, $payload);
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'userId' => ['The user id field is required.']
            ]
        ]);

        $user = factory(User::class)->create();

        $payload = [
            'bookId' => $book->id,
            'userId' => $user->id
        ];

        $response = $this->callHttpWithToken('POST', 'hearts', $token, $payload);
        $response->assertStatus(403);

        $this->assertDatabaseMissing('bookshelves', [
            'user_id' => $me->user->id,
            'book_id' => $book->id
        ]);

        $this->assertEquals($response->getData(), 'user_have_not_liked_book');

        $bookshelf = factory(Bookshelf::class)->create();

        $this->assertDatabaseHas('bookshelves', [
            'user_id' => $me->user->id,
            'book_id' => $book->id
        ]);

        $this->assertDatabaseMissing('bookshelves', [
            'user_id' => $user->id,
            'book_id' => $book->id
        ]);

        $response = $this->callHttpWithToken('POST', 'hearts', $token, $payload);
        $response->assertStatus(403);
        
        $this->assertEquals($response->getData(), 'partner_have_not_liked_book');

        $user->books()->attach($book);

        $this->assertDatabaseHas('bookshelves', [
            'user_id' => $user->id,
            'book_id' => $book->id
        ]);

        $this->assertDatabaseMissing('hearts', [
            'user_id'       => $me->user->id,
            'heart_user_id' => $user->id,
            'book_id'       => $book->id
        ]);

        $response = $this->callHttpWithToken('POST', 'hearts', $token, $payload);
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('hearts', [
            'user_id'       => $me->user->id,
            'heart_user_id' => $user->id,
            'book_id'       => $book->id
        ]);
    }

    /**
     * @group deleteHeart
     *
     */
    public function testDeleteHeart()
    {
        $me = $this->newUser(true);

        $token = $me->token;

        $book = factory(Book::class)->create();
        $bookshelf = factory(Bookshelf::class)->create();

        $user = factory(User::class)->create();
        $user->books()->attach($book);

        $response = $this->callHttpWithToken('DELETE', 'hearts/' . $user->id, $token);
        $response->assertStatus(403);

        $this->assertEquals($response->getData(), 'have_not_liked_user');

        \App\Heart::create([
            'user_id'       => $user->id,
            'heart_user_id' => $me->user->id,
            'book_id'       => $book->id
        ]);

        $this->assertDatabaseHas('hearts', [
            'user_id'       => $user->id,
            'heart_user_id' => $me->user->id,
            'book_id'       => $book->id
        ]);

        $response = $this->callHttpWithToken('DELETE', 'hearts/' . $user->id, $token);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('hearts', [
            'user_id'       => $user->id,
            'heart_user_id' => $me->user->id,
            'book_id'       => $book->id
        ]);

        \App\Heart::create([
            'user_id'       => $me->user->id,
            'heart_user_id' => $user->id,
            'book_id'       => $book->id
        ]);

        $this->assertDatabaseHas('hearts', [
            'user_id'       => $me->user->id,
            'heart_user_id' => $user->id,
            'book_id'       => $book->id
        ]);

        $response = $this->callHttpWithToken('DELETE', 'hearts/' . $user->id, $token);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('hearts', [
            'user_id'       => $me->user->id,
            'heart_user_id' => $user->id,
            'book_id'       => $book->id
        ]);
    }

    /**
     * @group getNotifications
     *
     */
    public function testGetNotifications()
    {
        $me = $this->newUser(true);

        $token = $me->token;

        $hearts = factory(\App\Heart::class, rand(1, 3))->create(['heart_user_id' => $me->user->id]);

        $response = $this->callHttpWithToken('GET', 'notifications', $token)
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'createdAt',
                    'user' => [
                        'id',
                        'name',
                        'slug',
                    ],
                    'book' => [
                        'id',
                        'title',
                        'slug',
                    ]
                ]
            ]);

        $this->assertEquals($hearts->count(), count($response->getdata()));

        foreach ($hearts as $heart) {
            $response->assertJsonFragment([
                'id'   => $heart->user->id,
                'name' => $heart->user->name,
                'slug' => $heart->user->slug,
            ]);

            $this->assertNotEmpty($heart->user->book);
        }
    }

    /**
     * @group getCountNotifications
     *
     */
    public function testGetCountNotifications()
    {
        $me = $this->newUser(true);

        $token = $me->token;

        $hearts = factory(\App\Heart::class, rand(1, 3))->create(['heart_user_id' => $me->user->id]);

        $response = $this->callHttpWithToken('GET', 'notifications/count', $token)
            ->assertStatus(200)
            ->assertJson(['count' => $hearts->count()]);
    }
}
