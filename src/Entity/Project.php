<?php


namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActual;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="projects")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="user")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true)
     */
    private $ownerUser;


    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function addUser($user): void
    {
        /** @var User $user */
        $user->addProject($this);
        $this->users->add($user);
    }

    public function removeUser($user): void
    {
        /** @var User $user */
        $user->removeGoal($this);
        $this->users->removeElement($user);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getIsActual()
    {
        return $this->isActual;
    }

    /**
     * @param mixed $isActual
     */
    public function setIsActual($isActual): void
    {
        $this->isActual = $isActual;
    }

    /**
     * @return mixed
     */
    public function getOwnerUser()
    {
        return $this->ownerUser;
    }

    /**
     * @param mixed $ownerUser
     */
    public function setOwnerUser($ownerUser): void
    {
        $this->ownerUser = $ownerUser;
    }
}