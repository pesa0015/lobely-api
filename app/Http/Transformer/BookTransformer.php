<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Book;

class BookTransformer extends Fractal\TransformerAbstract
{
    protected $availableIncludes = [
        'authors'
    ];

    public function transform(Book $book)
    {
        return [
            'title'         => $book->title,
            'originalTitle' => $book->original_title,
            'slug'          => $book->slug,
            'cover'         => $book->cover
        ];
    }

    /**
     * Include authors
     *
     * @return League\Fractal\CollectionResource
     */
    public function includeAuthors(Book $book)
    {
        $authors = $book->authors;

        return $this->collection($authors, new AuthorTransformer);
    }
}
