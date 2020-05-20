<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Timer::class, function (Faker $faker) {
    $category_names = ['English', 'Programming', 'UI UX Design'];
    $category_colors = ['#FA0202', '#FFC46D', '#5735CE'];
    return [
        'name' => $faker->realText($maxNbChars = 30, $indexSize = 1),
        'memo' => $faker->realText($maxNbChars = 140, $indexSize = 1),
        'user_id' => '1',
        'started_at' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'stopped_at' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'created_at' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'updated_at' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'category_id' => $faker->numberBetween($min = 1, $max = 3),
        'category_name' => $category_names[rand(0, 2)],
        'category_color' => $category_colors[rand(0, 2)]
    ];  
});
