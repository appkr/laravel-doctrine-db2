<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Name;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use LaravelDoctrine\ORM\Auth\Authenticatable;

class User implements AuthenticatableContract, CanResetPasswordContract, AuthorizableContract
{
    use Authenticatable, CanResetPassword, Authorizable, Timestamps;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var \App\Domain\ValueObjects\Name
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var ArrayCollection
     */
    protected $tasks;

    /**
     * @param Name $name
     * @param string $email
     */
    public function __construct(Name $name, $email)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function setName(Name $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param Task $task
     */
    public function addTask(Task $task)
    {
        if (! $this->tasks->contains($task)) {
            $task->setUser($this);
            $this->tasks->add($task);
        }
    }

    /**
     * @return ArrayCollection|Task
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}
