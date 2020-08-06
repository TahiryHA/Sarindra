<?php

namespace App\Entity;

use App\Repository\AbsenceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AbsenceRepository::class)
 */
class Absence
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="absences")
     */
    private $departement;

    /**
     * @ORM\Column(type="json")
     */
    private $present = [];

    /**
     * @ORM\Column(type="json")
     */
    private $absent = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getPresent(): ?array
    {
        return $this->present;
    }

    public function setPresent(array $present): self
    {
        $this->present = $present;

        return $this;
    }

    public function getAbsent(): ?array
    {
        return $this->absent;
    }

    public function setAbsent(array $absent): self
    {
        $this->absent = $absent;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
