<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelDoctrine\ORM\Configuration\Connections\ConnectionManager;

class DB2ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param \LaravelDoctrine\ORM\Configuration\Connections\ConnectionManager $connections
     */
    public function boot(ConnectionManager $connections)
    {
        $connections->extend('ibm_db2', function ($settings, \Illuminate\Foundation\Application $app) {
            return $settings;
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
