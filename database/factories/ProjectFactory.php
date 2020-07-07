<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Project;
use Illuminate\Support\Arr;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'owner_id' => factory('App\User')->create(),
        'visibility' => Arr::random(['public', 'internal', 'private']),
        'title' => $faker->sentence,
        'description' => $faker->paragraph
    ];
});
