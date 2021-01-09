<?php

declare(strict_types=1);

namespace App\Controller\Api\Game;

use App\Dto\ResponseDto\ResponseDTO;
use App\Entity\Game\Game;
use App\Entity\User;
use App\Security\Voter\GameVoter;
use App\Services\Game\GameService;
use App\Services\Response\Responser;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use RuntimeException;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/games", name="api.games")
 */
final class GameController extends AbstractController
{
    private GameService $gameService;

    private EntityManagerInterface $em;

    public function __construct(GameService $gameService, EntityManagerInterface $em)
    {
        $this->gameService = $gameService;
        $this->em = $em;
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
    public function searchAction(ParamFetcher $paramFetcher): ResponseDTO
    {
        $limit = (int) $paramFetcher->get('limit');
        $offset = (int) $paramFetcher->get('offset');

        [$games, $pagination] = $this->em->getRepository(Game::class)->getGamesWithPagination($limit, $offset);

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
    public function create(ParamFetcher $paramFetcher): ResponseDTO
    {
        $title = $paramFetcher->get('title');
        $type = $paramFetcher->get('type');
        $password = $paramFetcher->get('password');

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new RuntimeException('User does not exists');
        }

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
    public function currentGame(): ResponseDTO
    {
        $user = $this->getUser();
        $game = $this->em->getRepository(Game::class)->getCurrentUserGame($user);

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
    public function gameVisit(Game $game): ResponseDTO
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
    public function gameLeave(Game $game): ResponseDTO
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_LEAVE, $game);
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new RuntimeException('User does not exists');
        }

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
    public function gameJoin(Game $game, ParamFetcher $paramFetcher): ResponseDTO
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_JOIN, $game);

        $password = $paramFetcher->get('password');
        $teamId = (int) $paramFetcher->get('team');
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new RuntimeException('User does not exists');
        }

        $this->gameService->joinGame($user, $game, $teamId, $password);

        return Responser::wrapSuccess($game);
    }
}
