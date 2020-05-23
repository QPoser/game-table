<?php
declare(strict_types=1);

namespace App\Services\Game;

use App\Entity\Game\Room;
use App\Entity\Game\RoomPlayer;
use App\Entity\User;
use App\Exception\AppException;
use App\Services\Notification\RoomNotificationTemplateHelper;
use App\Services\Response\ErrorCode;
use App\Services\Validation\ValidationService;
use Doctrine\ORM\EntityManagerInterface;

class RoomService
{
    private EntityManagerInterface $em;

    private ValidationService $validator;

    private RoomNotificationTemplateHelper $roomNTH;

    public function __construct(EntityManagerInterface $em, ValidationService $validator, RoomNotificationTemplateHelper $roomNTH)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->roomNTH = $roomNTH;
    }

    public function createRoom(User $creator, string $title, int $slots, string $rules, ?string $password = null): Room
    {
        $this->em->beginTransaction();

        $room = new Room();
        $room->setTitle($title);
        $room->setSlots($slots);
        $room->setRules($rules);
        $room->setPassword($password);

        $this->validator->validateEntity($room);

        $this->em->persist($room);
        $this->em->flush($room);

        $this->createRoomPlayer($creator, $room, RoomPlayer::STATUS_MASTER);

        $this->em->commit();

        $this->roomNTH->createRoomCreatedNotifications($creator, $room);

        return $room;
    }

    public function createRoomPlayer(User $user, Room $room, string $status, ?string $role = null): RoomPlayer
    {
        $roomPlayer = new RoomPlayer();
        $roomPlayer->setPlayer($user);
        $roomPlayer->setRoom($room);
        $roomPlayer->setStatus($status);
        $roomPlayer->setRole($role);

        $this->validator->validateEntity($roomPlayer);

        $this->em->persist($roomPlayer);
        $this->em->flush($roomPlayer);

        return $roomPlayer;
    }

    public function leaveRoom(User $user, Room $room): void
    {
        $roomPlayer = $room->getRoomPlayerByUser($user);

        $roomPlayer->setStatus(RoomPlayer::STATUS_LEAVED);
        $this->em->flush($roomPlayer);
    }

    public function joinRoom(User $user, Room $room, ?string $password): void
    {
        if ($room->getPassword() && (!$password || $room->getPassword() !== $password)) {
            throw new AppException(ErrorCode::ROOM_PASSWORD_IS_INVALID);
        }

        $roomPlayer = $room->getRoomPlayerByUser($user);

        if (!$roomPlayer) {
            $this->createRoomPlayer($user, $room, RoomPlayer::STATUS_PLAYER);
            return;
        }

        $roomPlayer->setStatus(RoomPlayer::STATUS_PLAYER);
        $this->em->flush($roomPlayer);
    }
}