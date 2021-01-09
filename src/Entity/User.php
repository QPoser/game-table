<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"verifyToken"}, message="There is already an account with this verify token")
 */
class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const ROLES = [
        self::ROLE_USER => self::ROLE_USER,
        self::ROLE_ADMIN => self::ROLE_ADMIN,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Default", "Api", "AMQP"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     * @Groups({"Api", "AMQP"})
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="json")
     * @Groups({"UserRoles"})
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     * @Groups({"Exclude"})
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     * @Groups({"Api", "AMQP"})
     */
    private ?string $username = null;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Groups({"Exclude"})
     */
    private ?string $verifyToken = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"Exclude"})
     */
    private ?DateTime $vipUntilDate;

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

    public function addRole(string $role): self
    {
        if (in_array($role, self::ROLES, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): void
    {
    }

    public function eraseCredentials(): void
    {
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): string
    {
        return (string) $this->username;
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

    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->roles, true);
    }

    public function getFormattedRoles(): string
    {
        $callback = (static fn ($role) => mb_strtolower(str_replace('ROLE_', '', $role)));

        return implode(', ', array_map($callback, $this->roles));
    }

    public function getVipUntilDate(): ?DateTime
    {
        return $this->vipUntilDate;
    }

    public function setVipUntilDate(?DateTime $vipUntilDate): self
    {
        $this->vipUntilDate = $vipUntilDate;

        return $this;
    }

    /**
     * @Groups({"Api", "AMQP"})
     */
    public function isVip(): bool
    {
        return $this->vipUntilDate && $this->vipUntilDate > new DateTime('now');
    }
}
