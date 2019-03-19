<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Message;

class MessageTransformer extends Fractal\TransformerAbstract
{
    public function transform(Message $message)
    {
        return [
            'id'        => $message->id,
            'body'      => $message->body,
            'createdAt' => $message->created_at,
            'updatedAt' => $message->updated_at,
        ];
    }
}
