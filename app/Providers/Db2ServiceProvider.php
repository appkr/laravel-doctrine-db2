<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelDoctrine\ORM\Configuration\Connections\ConnectionManager;

class Db2ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param \LaravelDoctrine\ORM\Configuration\Connections\ConnectionManager $connections
     */
    public function boot(ConnectionManager $connections)
    {
        $connections->extend('db2', function (Application $app) {
            return [
                'url' => 'db2://db2inst1:password@localhost/sample'
            ];
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
