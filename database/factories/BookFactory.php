<?php

use App\Book;

$factory->define(Book::class, function (Faker\Generator $faker) {
    $title = ucfirst($faker->words(rand(1, 7), true));
    $originalTitle = rand(0, 1) ? $title : ucfirst($faker->words(rand(1, 7), true));

    $id = rand(1, 50);

    while (Book::where('cover', 'LIKE', '%' . $id . '.jpeg')->exists()) {
        $id = rand(1, 50);
    }

    return [
        'title'     => $title,
        'original_title' => $originalTitle,
        'slug'      => str_slug($title),
        'published' => $faker->dateTimeThisCentury->format('Y'),
        'pages'     => rand(200, 700),
        'cover'     => '/covers/' . $id . '.jpeg',
    ];
});
