<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParameterRepository")
 */
class Parameter
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $ID;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $NAME;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $CODE;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $VALUE;

    public function setId($em): self
    {
        $lastInserted = $em->createQueryBuilder()->select('count(p.ID)')->from('App:PARAMETER', 'p')
            ->getQuery()->getSingleScalarResult();
        $this->ID = $lastInserted + 1;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->ID;
    }

    public function getName(): ?string
    {
        return $this->NAME;
    }

    public function setName(string $name): self
    {
        $this->NAME = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->CODE;
    }

    public function setCode(string $code): self
    {
        $this->CODE = $code;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->VALUE;
    }

    public function setValue(string $value): self
    {
        $this->VALUE = $value;

        return $this;
    }
}
