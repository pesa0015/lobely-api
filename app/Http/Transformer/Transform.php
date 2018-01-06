<?php

namespace App\Http\Transformer;

use League\Fractal\Manager;
use League\Fractal\Resource;
use League\Fractal\Serializer\ArraySerializer;
use App\User;

class Transform
{
    private $fractal;
    private $user;
    private $book = null;

    public function __construct(User $user)
    {
        $this->fractal = new Manager();
        $this->fractal->setSerializer(new ArraySerializer());
        $this->user = $user;
    }

    public function setBook($book)
    {
        $this->book = $book;
    }

    public function getTransformer($transformer)
    {
        if (is_null($this->book)) {
            return new $transformer($this->user);
        } else {
            return new $transformer($this->user, $this->book);
        }
    }

    /**
     * Transform collection
     *
     */
    public function collection($rawCollection, $transformerController, $includes = false)
    {
        if ($includes) {
            $this->fractal->parseIncludes($includes);
        }
        $transformData = new Resource\Collection($rawCollection, $this->getTransformer($transformerController));

        $data = current($this->fractal->createData($transformData)->toArray());

        return $data;
    }

    /*
     * Transform item
     *
     */
    public function item($rawCollection, $transformerController, $includes = false)
    {
        if ($includes) {
            $this->fractal->parseIncludes($includes);
        }
        $transformData = new Resource\Item($rawCollection, $this->getTransformer($transformerController));

        $data = json_decode($this->fractal->createData($transformData)->toJson());

        return $data;
    }
}
