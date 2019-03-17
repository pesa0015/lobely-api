<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Message;

class MessageTransformer extends Fractal\TransformerAbstract
{
    public function transform(Message $message)
    {
        return [
            'id'     => $message->id,
            'userId' => $message->user_id,
            'body'   => $message->body,
        ];
    }
}
