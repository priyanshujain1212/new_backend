<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\SectionStatus;
use App\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Models\Section::class, function ( Faker $faker ) {

    return [
        'name'         => $faker->jobTitle,
        'slug'         => $faker->slug,
        'status'       => SectionStatus::ACTIVE,
        'creator_type' => 'App\User',
        'creator_id'   => User::get()->pluck('id')->random(),
        'editor_type'  => 'App\User',
        'editor_id'    => User::get()->pluck('id')->random(),
    ];
});
