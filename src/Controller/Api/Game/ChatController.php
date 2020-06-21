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
     * @SWG\Post(
     *     tags={"Chat"},
     *     @SWG\Response(
     *      response="200",
     *      description="Create game message",
     *      @Model(type=Message::class, groups={"Chat", "Api"})
     *     )
     * )
     */
    public function gameMessage(Game $game, Request $request): array
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_VISIT, $game);

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        $form->submit($request->request->get($form->getName()));

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $content = $request->request->get('content');

            $message = $this->chatService->createMessage($game, $user, $content);

            return Responser::wrapSuccess($message);
        }

        return Responser::wrapError('Message is not created', 1);
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

        [$messages, $pagination] = $this->getDoctrine()
            ->getRepository(Message::class)
            ->getMessagesByGameWithPagination($game, $limit, $offset);

        return Responser::wrapSuccess($messages, ['pagination' => $pagination]);
    }
}
