<?php

use Faker\Generator as Faker;

define("IMG_URLS", [
    "http://www.youxigt.com/d/file/2018-09-13/f02889fb3fec4e7ddb8fd9e00b6b781e.jpg",
    "https://i.ytimg.com/vi/OZkgIc4bU1I/maxresdefault.jpg",
    "https://strengthnet.com/wp-content/uploads/2016/12/Larry-Williams-Headshot.jpg"
]);

$factory->define(App\Article::class, function (Faker $faker) {
    return [
        'title' => $faker->text(50),
        'body' => $faker->text(200),
        'author' => $faker->text(10),
        'editor' => $faker->text(10),
        'img_urls' => "[".IMG_URLS[rand(0, sizeof(IMG_URLS) - 1)]."]",
        'view_count' => 0
    ];
});
