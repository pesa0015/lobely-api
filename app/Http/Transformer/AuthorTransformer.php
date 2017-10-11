<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\Author;

class AuthorTransformer extends Fractal\TransformerAbstract
{
    public function transform(Author $author)
    {
        return [
            'name' => $author->name
        ];
    }
}
