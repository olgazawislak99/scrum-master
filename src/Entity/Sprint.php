<?php

namespace App\Entity;

use App\Repository\SprintRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SprintRepository::class)
 * @ORM\Table(
 *      name="sprint"
 * )
 */
class Sprint
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
     * @ORM\Column(type="datetime")
     *
     */
    private $start_date;

    /**
     * @ORM\Column(type="datetime")[Doctrine\DBAL\Schema\SchemaException (20)]
     */
    private $end_date;

    /**
     * @var Goal
     * @ORM\OneToMany(targetEntity=Goal::class, mappedBy="goal", orphanRemoval=true, cascade={"remove"})
     */
    private $goal;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="sprint")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=true)
     */
    private $project;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDone;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project): void
    {
        $this->project = $project;
    }
}
