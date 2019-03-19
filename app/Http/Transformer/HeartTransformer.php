<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Heart;

class HeartTransformer extends Fractal\TransformerAbstract
{
    protected $availableIncludes = [
        'user', 'book', 'messages'
    ];

    public function transform(Heart $heart)
    {
        return [
            'id'        => $heart->id,
            'createdAt' => $heart->created_at,
            'status'    => $heart->status,
            'haveRead'  => $heart->have_read,
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

    /**
     * Include messages
     *
     * @return League\Fractal\CollectionResource
     */
    public function includeMessages(Heart $heart)
    {
        $messages = $heart->messages;

        return $this->collection($messages, new MessageTransformer);
    }
}
