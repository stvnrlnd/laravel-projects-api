<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Project;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'owner_id' => factory('App\User')->create(),
        'title' => $faker->sentence,
        'description' => $faker->paragraph
    ];
});
