<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Heart;

class HeartTransformer extends Fractal\TransformerAbstract
{
    protected $availableIncludes = [
        'user', 'book'
    ];

    public function transform(Heart $heart)
    {
        return [
            'createdAt' => $heart->created_at
        ];
    }

    /**
     * Include user
     *
     * @return League\Fractal\CollectionResource
     */
    public function includeUser(Heart $heart)
    {
        $user = $heart->user;

        return $this->item($user, new UserTransformer);
    }

    /**
     * Include book
     *
     * @return League\Fractal\CollectionResource
     */
    public function includeBook(Heart $heart)
    {
        $book = $heart->book;

        return $this->item($book, new BookTransformer);
    }
}
