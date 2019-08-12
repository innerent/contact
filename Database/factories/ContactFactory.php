<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Innerent\Contact\Entities\Contact::class, function (Faker $faker) {
    return [
        'name' => $faker->name
    ];
});
