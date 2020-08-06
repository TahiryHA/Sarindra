<?php

namespace App\Entity;

use App\Repository\PersonnelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=PersonnelRepository::class)
 */
class Personnel
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
     * @ORM\OneToMany(targetEntity=Conge::class, mappedBy="personnel")
     */
    private $conge;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="personnels")
     * @Assert\NotBlank()
     */
    private $departement;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="personnels")
     * @Assert\NotBlank()
     */
    private $categorie;


    public function __construct()
    {
        $this->conge = new ArrayCollection();
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

    /**
     * @return Collection|Conge[]
     */
    public function getConge(): Collection
    {
        return $this->conge;
    }

    public function addConge(Conge $conge): self
    {
        if (!$this->conge->contains($conge)) {
            $this->conge[] = $conge;
            $conge->setPersonnel($this);
        }

        return $this;
    }

    public function removeConge(Conge $conge): self
    {
        if ($this->conge->contains($conge)) {
            $this->conge->removeElement($conge);
            // set the owning side to null (unless already changed)
            if ($conge->getPersonnel() === $this) {
                $conge->setPersonnel(null);
            }
        }

        return $this;
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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

}
