<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'facebook_id' => 1,
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'gender' => 'male',
        'interested_in_gender' => 'female',
        'birth_date' => '1990-03-29',
        'bio' => $faker->sentences(2, true),
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Author::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name
    ];
});

$factory->define(App\Book::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->name,
        'original_title' => $faker->name,
        'slug' => $faker->name,
        'publisher' => $faker->name,
        'published' => rand(1961, (int) date('y')),
        'pages' => rand(129, 720),
        'cover' => $faker->name
    ];
});

$factory->define(App\Bookshelf::class, function (Faker\Generator $faker) {
    return [
        'book_id' => 1,
        'user_id' => 1
    ];
});
