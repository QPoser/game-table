<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\TimeStampTrait;
use App\Repository\FriendRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\FriendRequestRepository", repositoryClass=FriendRequestRepository::class)
 * @UniqueEntity(fields={"userFrom","userTo"}, message="There is friend request between this users allready")
 * @ORM\HasLifecycleCallbacks
 */
class FriendRequest
{
    public const STATUS_SENT = 'SENT';
    public const STATUS_DECLINED = 'DECLINED';
    public const STATUS_ACCEPTED = 'ACCEPTED';

    use TimeStampTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"Default", "Api"})
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="friendRequests")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Default", "Api"})
     */
    private ?UserInterface $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="friendRequests")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Default", "Api"})
     */
    private ?UserInterface $userTo;

    /**
     * @ORM\Column(type="text")
     * @Groups({"Default", "Api"})
     */
    private ?string $statusHistory = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserFrom(): ?UserInterface
    {
        return $this->userFrom;
    }

    public function setUserFrom(UserInterface $userFrom): self
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    public function getUserTo(): UserInterface
    {
        return $this->userTo;
    }

    public function setUserTo(UserInterface $userTo): self
    {
        $this->userTo = $userTo;

        return $this;
    }

    public function getStatusHistory(): ?string
    {
        return $this->statusHistory;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps(): void
    {
        $this->statusHistory .= PHP_EOL . $this->status;
    }

}
