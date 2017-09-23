<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Bookshelf;
use App\Book;
use App\User;

class BookshelfTransformer extends Fractal\TransformerAbstract
{
    protected $availableIncludes = [
        'book'
    ];

    public function transform(Bookshelf $bookshelf)
    {
        return [
            'user_id'    => $bookshelf->user_id,
            'book_id'    => $bookshelf->book_id,
            'created_at' => $bookshelf->created_at
        ];
    }

    /**
     * Include Book
     *
     * @return League\Fractal\ItemResource
     */
    public function includeBook(Bookshelf $bookshelf)
    {
        $book = $bookshelf->book;

        return $this->item($book, new BookTransformer);
    }
}
