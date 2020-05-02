<?php
declare(strict_types=1);

namespace App\Controller\Game;

use App\Entity\Game\Room;
use App\Form\RoomType;
use App\Security\Voter\RoomVoter;
use App\Services\Game\RoomService;
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

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
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

        return $this->render('game/room/room.html.twig', compact('room'));
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
     */
    public function roomJoin(Room $room): Response
    {
        $this->denyAccessUnlessGranted(RoomVoter::ATTRIBUTE_JOIN, $room);
        $user = $this->getUser();

        $this->roomService->joinRoom($user, $room);

        return $this->redirectToRoute('app.rooms.room.visit', ['id' => $room->getId()]);
    }
}
