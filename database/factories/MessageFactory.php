<?php

use App\Message;

$factory->define(Message::class, function (Faker\Generator $faker) {
    return [
        'body' => $faker->text(),
    ];
});
