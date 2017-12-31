<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Book;
use App\User;

class BookLikedTransformer extends Fractal\TransformerAbstract
{
    public function transform(Book $book, User $user)
    {
        return [
            'liked'   => true,
            'comment' => $book->bookshelf()->where('user_id', $user->id)->first()->comment
        ];
    }
}
