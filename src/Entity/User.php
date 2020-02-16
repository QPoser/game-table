<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"Default", "Api"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     * @Serializer\Groups({"Api"})
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Groups({"UserRoles"})
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     * @Serializer\Exclude
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     * @Serializer\Groups({"Api"})
     */
    private ?string $username = null;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     * @Serializer\Exclude
     */
    private ?string $verifyToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getVerifyToken(): ?string
    {
        return $this->verifyToken;
    }

    public function setVerifyToken(?string $verifyToken): self
    {
        $this->verifyToken = $verifyToken;

        return $this;
    }

    public function isVerified(): bool
    {
        return empty($this->verifyToken);
    }
}
