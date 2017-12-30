<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Book;

class BookLikedTransformer extends Fractal\TransformerAbstract
{
    public function transform(Book $book)
    {
        return [
            'liked' => true
        ];
    }
}
