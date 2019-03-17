<?php

namespace App\Http\Transformer;

use League\Fractal\Manager;
use League\Fractal\Resource;
use App\Serializers\Serializer;
use App\User;

class Transform
{
    private $fractal;
    private $user;
    private $book = null;

    public function __construct(User $user)
    {
        $this->fractal = new Manager();
        $this->fractal->setSerializer(new Serializer());
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
    public function collection($rawCollection, $transformerController, array $includes = [])
    {
        if ($includes) {
            $this->fractal->parseIncludes($includes);
        }

        $transformer = $this->getTransformer($transformerController);

        $transformData = new Resource\Collection($rawCollection, $transformer, implode(',', $includes));

        $data = $this->fractal->createData($transformData)->toArray();

        return $data;
    }

    /*
     * Transform item
     *
     */
    public function item($rawCollection, $transformerController, array $includes = [])
    {
        if ($includes) {
            $this->fractal->parseIncludes($includes);
        }

        $transformer = $this->getTransformer($transformerController);

        $transformData = new Resource\Item($rawCollection, $transformer, implode(',', $includes));

        $data = $this->fractal->createData($transformData)->toArray();

        return $data;
    }
}
