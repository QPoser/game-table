<?php
declare(strict_types=1);

namespace App\Controller\Api\Game;

use App\Entity\Game\Chat\Message;
use App\Entity\Game\Room;
use App\Form\MessageType;
use App\Security\Voter\RoomVoter;
use App\Services\Chat\ChatService;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 * @Route("/api/chat", name="api.chat")
 */
class ChatController extends AbstractController
{
    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * @Route("/{room}/message", name=".message", methods={"POST"})
     */
    public function roomMessage(Room $room, Request $request): Response
    {
        $this->denyAccessUnlessGranted(RoomVoter::ATTRIBUTE_VISIT, $room);

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        $form->submit($request->request->get($form->getName()));

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $content = $request->request->get('content');

            $this->chatService->createMessage($room, $user, $content);

            return new JsonResponse(Responser::wrapSuccess($message));
        }

        return new JsonResponse(Responser::wrapError('Message is not created', 1));
    }

    /**
     * @Route("/{room}/messages", name=".messages", methods={"GET"})
     * @Rest\View(serializerGroups={"Chat", "Api"})
     * @QueryParam(name="offset", nullable=true, default="60", requirements="\d+", strict=true)
     * @QueryParam(name="limit", nullable=true, default="60", requirements="\d+", strict=true)
     */
    public function getRoomMessages(Room $room, ParamFetcher $paramFetcher): array
    {
        $this->denyAccessUnlessGranted(RoomVoter::ATTRIBUTE_VISIT, $room);

        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');

        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(['room' => $room], ['id' => 'DESC'], $limit, $offset);

        return Responser::wrapSuccess($messages);
    }
}
