<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Book;

class BookTransformer extends Fractal\TransformerAbstract
{
    public function transform(Book $book)
    {
        return [
            'title'         => $book->title,
            'originalTitle' => $book->original_title,
            'slug'          => $book->slug,
            'cover'         => $book->cover
        ];
    }
}
