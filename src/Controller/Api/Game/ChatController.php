<?php
declare(strict_types=1);

namespace App\Controller\Api\Game;

use App\Entity\Game\Chat\Message;
use App\Entity\Game\Game;
use App\Form\MessageType;
use App\Security\Voter\GameVoter;
use App\Services\Chat\ChatService;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\RequestParam;

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
     * @Route("/{game}/message", name=".message", methods={"POST"})
     * @Rest\View(serializerGroups={"Chat", "Api"})
     * @RequestParam(name="content", requirements="\w+", nullable=false, strict=true, description="Message content")
     * @RequestParam(name="type", requirements="team|game", nullable=true, default="game", strict=true, description="Message type")
     * @SWG\Post(
     *     tags={"Chat"},
     *     @SWG\Response(
     *      response="200",
     *      description="Create game message",
     *      @Model(type=Message::class, groups={"Chat", "Api"})
     *     )
     * )
     */
    public function gameMessage(Game $game, ParamFetcher $paramFetcher): array
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_VISIT, $game);

        $content = $paramFetcher->get('content');
        $type = $paramFetcher->get('type');

        $message = $this->chatService->createMessage($game, $this->getUser(), $content, $type);

        return Responser::wrapSuccess($message);
    }

    /**
     * @Route("/{game}/messages", name=".messages", methods={"GET"})
     * @Rest\View(serializerGroups={"Chat", "Api"})
     * @QueryParam(name="offset", nullable=true, default="0", requirements="\d+", strict=true)
     * @QueryParam(name="limit", nullable=true, default="60", requirements="\d+", strict=true)
     * @SWG\Get(
     *     tags={"Chat"},
     *     @SWG\Response(
     *      response="200",
     *      description="Get game messages",
     *      @SWG\Schema(
     *          type="array",
     *          @Model(type=Message::class, groups={"Chat", "Api"})
     *      )
     *     )
     * )
     */
    public function getGameMessages(Game $game, ParamFetcher $paramFetcher): array
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_VISIT, $game);

        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');
        $user = $this->getUser();

        [$messages, $pagination] = $this->getDoctrine()
            ->getRepository(Message::class)
            ->getMessagesByGameWithPagination($game, $user, $limit, $offset);

        return Responser::wrapSuccess($messages, ['pagination' => $pagination]);
    }
}
