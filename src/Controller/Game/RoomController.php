<?php
declare(strict_types=1);

namespace App\Controller\Game;

use App\Entity\Game\Chat\Message;
use App\Entity\Game\Room;
use App\Form\RoomType;
use App\Security\Voter\RoomVoter;
use App\Services\Game\RoomService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rooms", name="app.rooms")
 */
class RoomController extends AbstractController
{
    private RoomService $roomService;

    private JWTTokenManagerInterface $JWTManager;

    public function __construct(RoomService $roomService, JWTTokenManagerInterface $JWTManager)
    {
        $this->roomService = $roomService;
        $this->JWTManager = $JWTManager;
    }

    /**
     * @Route("/", name="")
     */
    public function index(): Response
    {
        $rooms = $this->getDoctrine()->getRepository(Room::class)->findAll();

        return $this->render('game/room/index.html.twig', compact('rooms'));
    }

    /**
     * @Route("/create", name=".create")
     */
    public function create(Request $request): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $title = $form->get('title')->getData();
            $slots = $form->get('slots')->getData();
            $rules = $form->get('rules')->getData();
            $password = $form->get('password')->getData();

            $this->roomService->createRoom($user, $title, $slots, $rules, $password);

            return $this->redirectToRoute('app.rooms');
        }

        return $this->render('game/room/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name=".room.visit")
     */
    public function roomVisit(Room $room): Response
    {
        $this->denyAccessUnlessGranted(RoomVoter::ATTRIBUTE_VISIT, $room);

        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(['room' => $room], ['id' => 'DESC'], 60);
        $messages = array_reverse($messages);
        $token = $this->JWTManager->create($this->getUser());

        return $this->render('game/room/room.html.twig', compact('room', 'messages', 'token'));
    }

    /**
     * @Route("/{id}/leave", name=".room.leave", methods={"POST"})
     */
    public function roomLeave(Room $room): Response
    {
        $this->denyAccessUnlessGranted(RoomVoter::ATTRIBUTE_VISIT, $room);
        $user = $this->getUser();

        $this->roomService->leaveRoom($user, $room);

        return $this->redirectToRoute('app.rooms');
    }

    /**
     * @Route("/{id}/join", name=".room.join", methods={"POST"})
     * @Rest\RequestParam(name="password", requirements="\w+", nullable=true, strict=true, description="Password")
     */
    public function roomJoin(Room $room, ParamFetcher $paramFetcher): Response
    {
        $this->denyAccessUnlessGranted(RoomVoter::ATTRIBUTE_JOIN, $room);
        $password = $paramFetcher->get('password');
        $user = $this->getUser();

        $this->roomService->joinRoom($user, $room, $password);

        return $this->redirectToRoute('app.rooms.room.visit', ['id' => $room->getId()]);
    }
}
