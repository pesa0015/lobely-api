<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Book;
use App\User;

class BookshelfTransformer extends Fractal\TransformerAbstract
{
    protected $availableIncludes = [
        'authors'
    ];

    private $user;

    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    public function transform(Book $book)
    {
        return array_merge(
            (new BookTransformer)->transform($book),
            (new BookLikedTransformer)->transform($book, $this->user)
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
