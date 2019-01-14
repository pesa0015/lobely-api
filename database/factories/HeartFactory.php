<?php

use App\Heart;

$factory->define(Heart::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory('App\User')->create()->id,
        'book_id' => factory('App\Book')->create()->id,
        'have_read' => false,
    ];
});
