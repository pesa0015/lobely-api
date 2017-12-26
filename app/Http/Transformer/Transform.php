<?php

namespace App\Http\Transformer;

use League\Fractal\Manager;
use League\Fractal\Resource;
use League\Fractal\Serializer\ArraySerializer;

class Transform
{
    private $fractal;

    public function __construct()
    {
        $this->fractal = new Manager();
        $this->fractal->setSerializer(new ArraySerializer());
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
        $transformData = new Resource\Collection($rawCollection, $transformerController);

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
        $transformData = new Resource\Item($rawCollection, $transformerController);

        $data = json_decode($this->fractal->createData($transformData)->toJson());

        return $data;
    }
}
