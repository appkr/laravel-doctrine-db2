<?php

namespace App\Domain\Entities;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObjects\Name;
use App\Domain\ValueObjects\Email;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use LaravelDoctrine\ORM\Auth\Authenticatable;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User implements AuthenticatableContract, CanResetPasswordContract, AuthorizableContract
{
    use Authenticatable, CanResetPassword, Authorizable, Timestamps;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Entities\Task", mappedBy="user", cascade={"persist"})
     * @var ArrayCollection|\App\Domain\Entities\Task[]
     */
    protected $tasks;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $args = func_get_args();

        if (count($args) === 1 and is_array($args[0])) {
            $this->name = $args[0]['name'];
            $this->email = $args[0]['email'];
            $this->password = $args[0]['password'];
        } else {
            $this->name = $args[0];
            $this->email = $args[1];
            $this->password = $args[2];
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
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
     * @param \App\Domain\Entities\Task $task
     */
    public function addTask(\App\Domain\Entities\Task $task)
    {
        if (! $this->tasks->contains($task)) {
            $task->setUser($this);
            $this->tasks->add($task);
        }
    }

    /**
     * @return ArrayCollection|\App\Domain\Entities\Task
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}
