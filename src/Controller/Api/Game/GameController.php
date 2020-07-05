<?php
declare(strict_types=1);

namespace App\Controller\Api\Game;

use App\Entity\Game\Game;
use App\Security\Voter\GameVoter;
use App\Services\Game\GameService;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/games", name="api.games")
 */
class GameController extends AbstractController
{
    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * @Route("", name=".search", methods={"GET"})
     * @Rest\View(serializerGroups={"Api"})
     * @QueryParam(name="offset", nullable=true, default="0", requirements="\d+", strict=true)
     * @QueryParam(name="limit", nullable=true, default="20", requirements="\d+", strict=true)
     * @SWG\Get(
     *     tags={"Games"},
     *     @SWG\Response(
     *      response="200",
     *      description="Get games list",
     *      @SWG\Schema(
     *          type="array",
     *          @Model(type=Game::class, groups={"Api"})
     *      )
     *     )
     * )
     */
    public function searchAction(ParamFetcher $paramFetcher): array
    {
        $limit = (int)$paramFetcher->get('limit');
        $offset = (int)$paramFetcher->get('offset');

        [$games, $pagination] = $this->getDoctrine()->getRepository(Game::class)->getGamesWithPagination($limit, $offset);

        return Responser::wrapSuccess($games, ['pagination' => $pagination]);
    }

    /**
     * @Route("/create", name=".create", methods={"POST"})
     * @Rest\View(serializerGroups={"Api", "GameMessages"})
     * @RequestParam(name="title", requirements="\w+", nullable=false, strict=true, description="Game title")
     * @RequestParam(name="type", requirements="quiz", nullable=false, strict=true, description="Game type")
     * @RequestParam(name="password", requirements="\w+", nullable=true, strict=true, description="Game password")
     * @SWG\Post(
     *     tags={"Games"},
     *     @SWG\Response(
     *      response="200",
     *      description="Create game",
     *      @Model(type=Game::class, groups={"Api", "GameMessages"})
     *     )
     * )
     */
    public function create(ParamFetcher $paramFetcher): array
    {
        $user = $this->getUser();
        $title = $paramFetcher->get('title');
        $type = $paramFetcher->get('type');
        $password = $paramFetcher->get('password');

        $game = $this->gameService->createGame($title, $type, $user, $password);

        return Responser::wrapSuccess($game);
    }

    /**
     * @Route("/current", name=".current", methods={"GET"})
     * @Rest\View(serializerGroups={"Api"})
     * @SWG\Get(
     *     tags={"Games"},
     *     @SWG\Response(
     *      response="200",
     *      description="Get current game",
     *      @Model(type=Game::class, groups={"Api"})
     *     )
     * )
     */
    public function currentGame(): array
    {
        $user = $this->getUser();
        $game = $this->getDoctrine()->getRepository(Game::class)->getCurrentUserGame($user);

        return Responser::wrapSuccess($game);
    }

    /**
     * @Route("/{id}", name=".visit", methods={"GET"})
     * @Rest\View(serializerGroups={"Api"})
     * @SWG\Get(
     *     tags={"Games"},
     *     @SWG\Response(
     *      response="200",
     *      description="Visit game",
     *      @Model(type=Game::class, groups={"Api"})
     *     )
     * )
     */
    public function gameVisit(Game $game): array
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_VISIT, $game);

        return Responser::wrapSuccess($game);
    }

    /**
     * @Route("/{id}/leave", name=".game.leave", methods={"POST"})
     * @Rest\View
     * @SWG\Post(
     *     tags={"Games"},
     *     @SWG\Response(
     *      response="200",
     *      description="Leave game"
     *     )
     * )
     */
    public function gameLeave(Game $game): array
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_LEAVE, $game);
        $user = $this->getUser();

        $this->gameService->leaveGame($user, $game);

        return Responser::wrapSuccess(true);
    }

    /**
     * @Route("/{id}/join", name=".game.join", methods={"POST"})
     * @Rest\View(serializerGroups={"Api", "GameMessages"})
     * @Rest\RequestParam(name="password", requirements="\w+", nullable=true, strict=true, description="Password")
     * @Rest\RequestParam(name="team", requirements="\d+", nullable=true, strict=true, description="Team id")
     * @SWG\Post(
     *     tags={"Games"},
     *     @SWG\Response(
     *      response="200",
     *      description="Join game",
     *      @Model(type=Game::class, groups={"Api", "GameMessages"})
     *     )
     * )
     */
    public function gameJoin(Game $game, ParamFetcher $paramFetcher): array
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_JOIN, $game);

        $password = $paramFetcher->get('password');
        $teamId = (int)$paramFetcher->get('team');
        $user = $this->getUser();

        $this->gameService->joinGame($user, $game, $teamId, $password);

        return Responser::wrapSuccess($game);
    }
}
