<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Book;

class BookshelfTransformer extends Fractal\TransformerAbstract
{
    protected $availableIncludes = [
        'authors'
    ];

    public function transform(Book $book)
    {
        return array_merge(
            (new BookTransformer)->transform($book),
            (new BookLikedTransformer)->transform($book)
        );
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
