<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Book;

class UserBooksTest extends TestCase
{
    /**
     * @group showUser
     *
     */
    public function testShowUser()
    {
        $me      = $this->newUser(true);
        $newUser = $this->newUser(true);

        $token = $newUser->token;
        $user  = $newUser->user;

        $user->books()->saveMany(factory(Book::class, rand(1, 3))->create());

        $response = $this->callHttpWithToken('GET', 'user/' . $user->slug, $token);
        $response->assertStatus(200);
        $response->assertJson([
            'id'                 => $user->id,
            'name'               => $user->name,
            'slug'               => $user->slug,
            'email'              => $user->email,
            'img'                => $user->profile_img,
            'gender'             => $user->gender,
            'interestedInGender' => $user->interested_in_gender,
            'birthDate'          => $user->birth_date,
            'bio'                => $user->bio,
            'heart'              => $user->heartsToPartner->first() || $user->heartsToMe->first()
        ]);
    }

    /**
     * @group showUserWithBook
     *
     */
    public function testShowUserWithBook()
    {
        $me      = $this->newUser(true);
        $newUser = $this->newUser(true);

        $token = $newUser->token;
        $user  = $newUser->user;

        $user->books()->saveMany(factory(Book::class, rand(1, 3))->create());

        $book = $user->books()->first();

        $response = $this->callHttpWithToken('GET', 'user/' . $user->slug . '?book=' . $book->slug, $token);
        $response->assertStatus(200);
        $response->assertJson([
            'id'                 => $user->id,
            'name'               => $user->name,
            'slug'               => $user->slug,
            'email'              => $user->email,
            'img'                => $user->profile_img,
            'gender'             => $user->gender,
            'interestedInGender' => $user->interested_in_gender,
            'birthDate'          => $user->birth_date,
            'bio'                => $user->bio,
            'heart'              => $user->heartsToPartner->first() || $user->heartsToMe->first(),
            'like'               => [
                'comment'        => $book->bookshelf()->where('user_id', $user->id)->where('book_id', $book->id)->first()->comment,
            ],
            'book'               => [
                'title'          => $book->title,
            ]
        ]);
    }
}
