<?php

namespace App\Providers;

use App\Domain\Entities\Task;
use App\Domain\Repositories\TaskRepository;
use App\Infrastructure\Repositories\DoctrineTaskRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use LaravelDoctrine\ORM\Auth\DoctrineUserProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TaskRepository::class, function (Application $app) {
            return new DoctrineTaskRepository(
                $app->make('em'),
                new ClassMetadata(Task::class)
            );
        });

        $this->app->make('auth')->provider('doctrine', function ($app, $config) {
            $entity = $config['model'];
            $em = $app['registry']->getManagerForClass($entity);

            if (!$em) {
                throw new InvalidArgumentException("No EntityManager is set-up for {$entity}");
            }

            return new DoctrineUserProvider(
                $app['hash'],
                $em,
                $entity
            );
        });
    }
}
