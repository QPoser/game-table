<?php

declare(strict_types=1);

namespace App\Controller\Api\Game;

use App\Dto\RequestDto\GameChat\GameMessageRequest;
use App\Dto\RequestDto\PaginationRequest;
use App\Dto\ResponseDto\ResponseDTO;
use App\Entity\Game\Chat\Message;
use App\Entity\Game\Game;
use App\Security\Voter\GameVoter;
use App\Services\Chat\ChatService;
use App\Services\Response\Responser;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/chat", name="api.chat")
 */
final class ChatController extends AbstractController
{
    private ChatService $chatService;

    private EntityManagerInterface $em;

    public function __construct(ChatService $chatService, EntityManagerInterface $em)
    {
        $this->chatService = $chatService;
        $this->em = $em;
    }

    /**
     * @Route("/{game}/message", name=".message", methods={"POST"})
     * @Rest\View(serializerGroups={"Chat", "Api"})
     * @SWG\Post(
     *     tags={"Chat"},
     *     @SWG\Response(
     *      response="200",
     *      description="Create game message",
     *      @Model(type=Message::class, groups={"Chat", "Api"})
     *     ),
     *     @SWG\Parameter(name="body", in="body", @Model(type=GameMessageRequest::class)))
     * )
     */
    public function gameMessage(Game $game, GameMessageRequest $gameMessageRequest): ResponseDTO
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_VISIT, $game);

        $message = $this->chatService->createMessage($game, $this->getUser(), $gameMessageRequest);

        return Responser::wrapSuccess($message);
    }

    /**
     * @Route("/{game}/messages", name=".messages", methods={"GET"})
     * @Rest\View(serializerGroups={"Chat", "Api"})
     * @SWG\Get(
     *     tags={"Chat"},
     *     @SWG\Response(
     *      response="200",
     *      description="Get game messages",
     *      @SWG\Schema(
     *          type="array",
     *          @Model(type=Message::class, groups={"Chat", "Api"})
     *      )
     *     ),
     *     @SWG\Parameter(name="body", in="body", @Model(type=PaginationRequest::class)))
     * )
     */
    public function getGameMessages(Game $game, PaginationRequest $paginationRequest): ResponseDTO
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_VISIT, $game);
        $user = $this->getUser();

        [$messages, $pagination] = $this
            ->em
            ->getRepository(Message::class)
            ->getMessagesByGameWithPagination($game, $user, $paginationRequest);

        return Responser::wrapSuccess($messages, ['pagination' => $pagination]);
    }
}
