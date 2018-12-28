<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\User;
use App\Book;

class UserTransformer extends Fractal\TransformerAbstract
{
    protected $availableIncludes = [
        'like', 'books', 'book'
    ];

    private $book;

    public function __construct(User $user, Book $book = null)
    {
        $this->book = $book;
    }

    public function transform(User $user)
    {
        return [
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
        ];
    }

    public function includeLike(User $user)
    {
        $comment = \App\Bookshelf::where('user_id', $user->id)->where('book_id', $this->book->id)->first();

        return $this->item($comment, new BookCommentTransformer);
    }

    public function includeBooks(User $user)
    {
        $books = $user->books;

        return $this->collection($books, new BookTransformer);
    }

    public function includeBook()
    {
        return $this->item($this->book, new BookTransformer);
    }
}
