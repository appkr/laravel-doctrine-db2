<?php

use App\Domain\Entities\Task;

class ExampleTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('doctrine:schema:create');
        $this->createUser();
    }

    public function createUser()
    {
        $user = new \App\Domain\Entities\User(
            'Foo',
            'foo@bar.com',
            bcrypt('password')
        );

        \EntityManager::persist($user);
        \EntityManager::flush();

        $this->user = $user;

        return $this;
    }

    public function test_tasks_are_displayed_on_the_dashboard()
    {
        entity(Task::class)->create(['name' => 'Task 1']);
        entity(Task::class)->create(['name' => 'Task 2']);
        entity(Task::class)->create(['name' => 'Task 3']);

        $this->visit('/')
            ->see('Task 1')
            ->see('Task 2')
            ->see('Task 3');
    }

    public function test_tasks_can_be_created()
    {
        $this->visit('/')->dontSee('Task 1');

        $this->actingAs($this->user)
            ->visit('/')
            ->type('Task 1', 'name')
            ->press('Add Task')
            ->see('Task 1');
    }

    public function test_long_tasks_cant_be_created()
    {
        $this->actingAs($this->user)
            ->visit('/')
            ->type(str_random(300), 'name')
            ->press('Add Task')
            ->see('Whoops!');
    }
}
