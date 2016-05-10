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

use App\Domain\Entities\Task;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\Name;
use Faker\Generator;

$factory->define(User::class, function (Generator $faker) {
    return [
        'name' => new Name($faker->firstName, $faker->lastName),
        'email' => $faker->safeEmail,
        'password' => bcrypt('password'),
        'remember_token' => str_random(60),
    ];
});

$factory->define(Task::class, function (Generator $faker) {
    $users = app(UserRepository::class)->all();

    return [
        'name' => $faker->sentence,
        'user' => $faker->randomElement(collect($users)->toArray())
    ];
});

