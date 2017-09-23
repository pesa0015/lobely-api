<?php

namespace App\Http\Transformer;

use League\Fractal\Manager;
use League\Fractal\Resource;
use League\Fractal\Serializer\ArraySerializer;

class Transform
{
    /**
     * Transform collection
     *
     */
    public function collection($rawCollection, $transformerController, $includes = false)
    {
        $fractal = new Manager();
        $fractal->setSerializer(new ArraySerializer());
        if ($includes) {
            $fractal->parseIncludes($includes);
        }
        $transformData = new Resource\Collection($rawCollection, $transformerController);

        $data = current($fractal->createData($transformData)->toArray());

        return $data;
    }

    /*
     * Transform item
     *
     */
    public function item($rawCollection, $transformerController, $includes = false)
    {
        $fractal = new Manager();
        $fractal->setSerializer(new ArraySerializer());
        if ($includes) {
            $fractal->parseIncludes($includes);
        }
        $transformData = new Resource\Item($rawCollection, $transformerController);

        $data = json_decode($fractal->createData($transformData)->toJson());

        return $data;
    }
}
