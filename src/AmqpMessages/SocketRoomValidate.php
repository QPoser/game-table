<?php
declare(strict_types=1);

namespace App\AmqpMessages;

use App\Entity\Game\Room;
use Symfony\Component\Serializer\Annotation\Groups;

class SocketRoomValidate
{
    /**
     * @Groups({"AMPQ"})
     */
    private ?string $socketId = null;

    /**
     * @Groups({"AMPQ"})
     */
    private ?Room $room = null;

    public function __construct(string $socketId, Room $room)
    {
        $this->socketId = $socketId;
        $this->room = $room;
    }

    public function getSocketId(): ?string
    {
        return $this->socketId;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }
}