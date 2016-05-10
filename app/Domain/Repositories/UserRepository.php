<?php

namespace App\Domain\Repositories;

interface UserRepository
{
    public function all($orderField = 'id', $order = 'ASC');

    public function find($id, $lockMode = null, $lockVersion = null);
}
