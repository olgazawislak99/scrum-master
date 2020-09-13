<?php

namespace App\Entity;

use App\Repository\GoalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GoalRepository::class)
 */
class Goal
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
     * @ORM\ManyToOne(targetEntity=Sprint::class, inversedBy="goal")
     * @ORM\JoinColumn(name="sprint_id", referencedColumnName="id", nullable=true)
     */
    private $sprint;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDone;


    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="goals")
     */
    private $users;

    /**
     * @ORM\Column(type="text", length=16383, nullable=true)
     */
    private $goalDesc;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inBacklog;

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
        $user->addGoal($this);
        $this->users->add($user);
    }

    public function removeUser($user): void
    {
        /** @var User $user */
        $user->removeGoal($this);
        $this->users->removeElement($user);
    }

    public function getIsDone()
    {
        return $this->isDone;
    }

    public function setIsDone($isDone): void
    {
        $this->isDone = $isDone;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSprint(): ?Sprint
    {
        return $this->sprint;
    }

    public function setSprint(?Sprint $sprint): self
    {
        $this->sprint = $sprint;

        return $this;
    }

    public function getGoalDesc(): ?string
    {
        return $this->goalDesc;
    }

    public function setGoalDesc(?string $goalDesc): self
    {
        $this->goalDesc = $goalDesc;

        return $this;
    }

    public function getInBacklog(): ?bool
    {
        return $this->inBacklog;
    }

    public function setInBacklog(bool $inBacklog): self
    {
        $this->inBacklog = $inBacklog;

        return $this;
    }
}
