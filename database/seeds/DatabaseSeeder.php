<?php

use App\Domain\Entities\Task;
use App\Domain\Entities\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        entity(User::class)->create();
        entity(User::class)->create();
        entity(User::class)->create();
        entity(Task::class)->create(['name' => 'Task 1']);
        entity(Task::class)->create(['name' => 'Task 2']);
        entity(Task::class)->create(['name' => 'Task 3']);
    }
}
