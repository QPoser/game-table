<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

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

    use TimeStampTrait;

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
     * @ORM\ManyToMany(targetEntity="App\Entity\Game\RoomPlayer", mappedBy="player")
     * @Groups({"Exclude"})
     */
    private Collection $roomPlayers;

    public function __construct()
    {
        $this->roomPlayers = new ArrayCollection();
    }

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
        if (in_array($role, self::ROLES)) {
            $this->roles[] = $role;
        }

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

    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->roles, true);
    }

    public function getFormattedRoles(): string
    {
        $callback = (fn($role) => mb_strtolower(str_replace('ROLE_', '', $role)));

        return implode(', ', array_map($callback, $this->roles));
    }
}
