<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nameSession = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $beginSession = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $finishSession = null;

    #[ORM\Column]
    private ?int $nbPlace = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user = null;

    /**
     * @var Collection<int, Intern>
     */
    #[ORM\ManyToMany(targetEntity: Intern::class, inversedBy: 'sessions')]
    private Collection $interns;

    /**
     * @var Collection<int, Program>
     */
    #[ORM\OneToMany(targetEntity: Program::class, mappedBy: 'session', orphanRemoval: true)]
    private Collection $programs;

    public function __construct()
    {
        $this->interns = new ArrayCollection();
        $this->programs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameSession(): ?string
    {
        return $this->nameSession;
    }

    public function setNameSession(string $nameSession): static
    {
        $this->nameSession = $nameSession;

        return $this;
    }

    public function getBeginSession(): ?\DateTimeInterface
    {
        return $this->beginSession;
    }

    public function setBeginSession(\DateTimeInterface $beginSession): static
    {
        $this->beginSession = $beginSession;

        return $this;
    }

    public function getFinishSession(): ?\DateTimeInterface
    {
        return $this->finishSession;
    }

    public function setFinishSession(\DateTimeInterface $finishSession): static
    {
        $this->finishSession = $finishSession;

        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): static
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Intern>
     */
    public function getInterns(): Collection
    {
        return $this->interns;
    }

    public function addIntern(Intern $intern): static
    {
        if (!$this->interns->contains($intern)) {
            $this->interns->add($intern);
        }

        return $this;
    }

    public function removeIntern(Intern $intern): static
    {
        $this->interns->removeElement($intern);

        return $this;
    }

    /**
     * @return Collection<int, Program>
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): static
    {
        if (!$this->programs->contains($program)) {
            $this->programs->add($program);
            $program->setSession($this);
        }

        return $this;
    }

    public function removeProgram(Program $program): static
    {
        if ($this->programs->removeElement($program)) {
            // set the owning side to null (unless already changed)
            if ($program->getSession() === $this) {
                $program->setSession(null);
            }
        }

        return $this;
    }
}
