<?php
declare(strict_types=1);

namespace App\Controller\Api\Game;

use App\Entity\Game\Chat\Message;
use App\Entity\Game\Room;
use App\Form\RoomType;
use App\Security\Voter\RoomVoter;
use App\Services\Game\RoomService;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/rooms", name="api.rooms")
 */
class RoomController extends AbstractController
{
    private RoomService $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    /**
     * @Route("", name=".search", methods={"GET"})
     * @Rest\View(serializerGroups={"Api"})
     * @QueryParam(name="offset", nullable=true, default="0", requirements="\d+", strict=true)
     * @QueryParam(name="limit", nullable=true, default="20", requirements="\d+", strict=true)
     * @SWG\Get(
     *     tags={"Rooms"},
     *     @SWG\Response(
     *      response="200",
     *      description="Get rooms list",
     *      @SWG\Schema(
     *          type="array",
     *          @Model(type=Room::class, groups={"Api"})
     *      )
     *     )
     * )
     */
    public function searchAction(ParamFetcher $paramFetcher): array
    {
        $limit = (int)$paramFetcher->get('limit');
        $offset = (int)$paramFetcher->get('offset');

        [$rooms, $pagination] = $this->getDoctrine()->getRepository(Room::class)->getRoomsWithPagination($limit, $offset);

        return Responser::wrapSuccess($rooms, ['pagination' => $pagination]);
    }

    /**
     * @Route("/create", name=".create", methods={"POST"})
     * @Rest\View(serializerGroups={"Api", "RoomMessages"})
     * @RequestParam(name="title", requirements="\w+", nullable=false, strict=true, description="Room title")
     * @RequestParam(name="slots", requirements="\d+", nullable=false, strict=true, description="Room slots (From 1 to 16)")
     * @RequestParam(name="rules", requirements="\w+", nullable=true, strict=true, description="Room rules")
     * @RequestParam(name="password", requirements="\w+", nullable=true, strict=true, description="Room password")
     * @SWG\Post(
     *     tags={"Rooms"},
     *     @SWG\Response(
     *      response="200",
     *      description="Create room",
     *      @Model(type=Room::class, groups={"Api", "RoomMessages"})
     *     )
     * )
     */
    public function create(ParamFetcher $paramFetcher): array
    {
        $user = $this->getUser();
        $title = $paramFetcher->get('title');
        $slots = (int)$paramFetcher->get('slots');
        $rules = $paramFetcher->get('rules');
        $password = $paramFetcher->get('password');

        $room = $this->roomService->createRoom($user, $title, $slots, $rules, $password);

        return Responser::wrapSuccess($room);
    }

    /**
     * @Route("/{id}", name=".visit", methods={"GET"})
     * @Rest\View(serializerGroups={"Api", "RoomMessages"})
     * @SWG\Get(
     *     tags={"Rooms"},
     *     @SWG\Response(
     *      response="200",
     *      description="Visit room",
     *      @Model(type=Room::class, groups={"Api", "RoomMessages"})
     *     )
     * )
     */
    public function roomVisit(Room $room): array
    {
        $this->denyAccessUnlessGranted(RoomVoter::ATTRIBUTE_VISIT, $room);

        return Responser::wrapSuccess($room);
    }

    /**
     * @Route("/{id}/leave", name=".room.leave", methods={"POST"})
     * @SWG\Post(
     *     tags={"Rooms"},
     *     @SWG\Response(
     *      response="200",
     *      description="Leave room"
     *     )
     * )
     */
    public function roomLeave(Room $room): array
    {
        $this->denyAccessUnlessGranted(RoomVoter::ATTRIBUTE_VISIT, $room);
        $user = $this->getUser();

        $this->roomService->leaveRoom($user, $room);

        return Responser::wrapSuccess(true);
    }

    /**
     * @Route("/{id}/join", name=".room.join", methods={"POST"})
     * @Rest\View(serializerGroups={"Api", "RoomMessages"})
     * @Rest\RequestParam(name="password", requirements="\w+", nullable=true, strict=true, description="Password")
     * @SWG\Post(
     *     tags={"Rooms"},
     *     @SWG\Response(
     *      response="200",
     *      description="Join room",
     *      @Model(type=Room::class, groups={"Api", "RoomMessages"})
     *     )
     * )
     */
    public function roomJoin(Room $room, ParamFetcher $paramFetcher): array
    {
        $this->denyAccessUnlessGranted(RoomVoter::ATTRIBUTE_JOIN, $room);
        $password = $paramFetcher->get('password');
        $user = $this->getUser();

        $this->roomService->joinRoom($user, $room, $password);

        return Responser::wrapSuccess($room);
    }
}
