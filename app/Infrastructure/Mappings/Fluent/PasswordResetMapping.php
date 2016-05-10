<?php

namespace App\Infrastructure\Mappings\Fluent;

use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;

class PasswordResetMapping extends EntityMapping
{
    /**
     * Returns the fully qualified name of the class that this mapper maps.
     *
     * @return string
     */
    public function mapFor()
    {
        return \LaravelDoctrine\ORM\Auth\Passwords\PasswordReminder::class;
    }

    /**
     * Load the object's metadata through the Metadata Builder object.
     *
     * @param Fluent $builder
     */
    public function map(Fluent $builder)
    {
        $builder->table('password_resets');
        $builder->string('email');
        $builder->string('token')->primary();
        $builder->dateTime('createdAt')->timestampable()->onCreate();
    }
}
