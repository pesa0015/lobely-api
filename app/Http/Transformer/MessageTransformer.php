<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Message;

class MessageTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'user'
    ];

    public function transform(Message $message)
    {
        return [
            'id'        => $message->id,
            'body'      => $message->body,
            'createdAt' => $message->created_at,
            'updatedAt' => $message->updated_at,
        ];
    }

    /**
     * Include user
     *
     * @return League\Fractal\CollectionResource
     */
    public function includeUser(Message $message)
    {
        $user = $message->user;

        return $this->item($user, new UserTransformer);
    }
}
