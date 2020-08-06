<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"USERNAME"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $MATRICULE;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $IMAGE;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $USERNAME;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $EMAIL;

    /**
     * @ORM\Column(type="json")
     */
    private $ROLES = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PASSWORD;

    /**
     * @var string le token qui servira lors de l'oubli de mot de passe
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $RESET_TOKEN;


    public function getEmail(): ?string
    {
        return $this->EMAIL;
    }

    public function setEmail(string $email): self
    {
        $this->EMAIL = $email;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->IMAGE;
    }

    public function setImage(?string $image): self
    {
        $this->IMAGE = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getResetToken(): string
    {
        return $this->RESET_TOKEN;
    }

    /**
     * @param string $resetToken
     */
    public function setResetToken(?string $resetToken): void
    {
        $this->RESET_TOKEN = $resetToken;
    }

    public function getUsername(): ?string
    {
        return $this->USERNAME;
    }

    public function setUsername(string $username): self
    {
        $this->USERNAME = $username;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->MATRICULE;
    }

    public function setMatricule(string $matricule): self
    {
        $this->MATRICULE = $matricule;

        return $this;
    }
    
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->ROLES;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->ROLES = $roles;

        return $this;
    }
    

     /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->PASSWORD;
    }

    public function setPassword(string $password): self
    {
        $this->PASSWORD = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function hasRole(string $role): bool
    {
        $roles = ['ROLE_SUPER_ADMIN'];
        return in_array($role, $roles);
    }

}
