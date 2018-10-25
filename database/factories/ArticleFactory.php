<?php

use Faker\Generator as Faker;

$factory->define(App\Article::class, function (Faker $faker) {
    return [
        'title' => $faker->text(50),
        'body' => $faker->text(200),
        'author' => $faker->text(10),
        'editor' => $faker->text(10),
        'img_urls' => "[]",
        'view_count' => 0
    ];
});
