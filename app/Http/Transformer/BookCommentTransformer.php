<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Bookshelf;

class BookCommentTransformer extends Fractal\TransformerAbstract
{
    public function transform(Bookshelf $bookshelf)
    {
        return [
            'comment' => $bookshelf->comment
        ];
    }
}
