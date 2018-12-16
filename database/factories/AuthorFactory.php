<?php

use App\Author;

$factory->define(Author::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName() . ' ' . $faker->lastName,
    ];
});
