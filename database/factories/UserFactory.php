<?php

use App\User;

$factory->define(User::class, function (Faker\Generator $faker) {
    $key = rand(0, 1);

    $genders = [
        'm' => 'men', 'f' => 'women'
    ];

    $gender = array_keys($genders)[$key];
    $name = $faker->firstName() . ' ' . $faker->lastName;

    return [
        'facebook_id' => null,
        'name'        => $name,
        'slug'        => User::generateSlug($name),
        'email'       => $faker->unique()->safeEmail,
        'gender'      => array_keys($genders)[$key],
        'interested_in_gender' => array_keys($genders)[!$key],
        'profile_img' => 'https://randomuser.me/api/portraits/' . $genders[$gender] . '/' . rand(0, 100) . '.jpg',
        'birth_date'  => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
        'bio'         => $faker->sentences(2, true),
        'password'    => bcrypt('test'),
    ];
});
