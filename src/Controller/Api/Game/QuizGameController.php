<?php

declare(strict_types=1);

namespace App\Controller\Api\Game;

use App\Dto\ResponseDto\ResponseDTO;
use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\User;
use App\Security\Voter\QuizGameVoter;
use App\Services\Game\Quiz\QuizGameService;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use RuntimeException;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/game/quiz", name="api.game.quiz")
 */
final class QuizGameController extends AbstractController
{
    private QuizGameService $quizGameService;

    public function __construct(QuizGameService $quizGameService)
    {
        $this->quizGameService = $quizGameService;
    }

    /**
     * @Route("/phases", name=".phases", methods={"GET"})
     * @Rest\View(serializerGroups={"Api"})
     * @SWG\Get(
     *     tags={"Quiz game"},
     *     @SWG\Response(
     *      response="200",
     *      description="Get available quiz phases",
     *      @SWG\Schema(
     *           type="array",
     *           @SWG\Items(
     *              type="string",
     *           )
     *      )
     *     )
     * )
     */
    public function getPhases(): ResponseDTO
    {
        return Responser::wrapSuccess(BasePhase::getFormattedTypes());
    }

    /**
     * @Route("/{id}/phase", name=".phase.select", methods={"POST"})
     * @Rest\View(serializerGroups={"Api"})
     * @Rest\RequestParam(name="phase_type", requirements="(questions|prices)", nullable=false, strict=true, description="Phase type")
     * @SWG\Post(
     *     tags={"Quiz game"},
     *     @SWG\Response(
     *      response="200",
     *      description="Select phase for quiz"
     *     )
     * )
     */
    public function selectPhase(QuizGame $game, ParamFetcher $paramFetcher): ResponseDTO
    {
        $this->denyAccessUnlessGranted(QuizGameVoter::ATTRIBUTE_SELECT_PHASE, $game);

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new RuntimeException('User does not exists');
        }

        $phaseType = $paramFetcher->get('phase_type');
        $this->quizGameService->addPhase($game, $phaseType, $user);

        return Responser::wrapSuccess(true);
    }

    /**
     * @Route("/{id}/answer", name=".phase.answer", methods={"POST"})
     * @Rest\View(serializerGroups={"Api"})
     * @Rest\RequestParam(name="answer", nullable=false, strict=true, description="Answer (any string)")
     * @SWG\Post(
     *     tags={"Quiz game"},
     *     @SWG\Response(
     *      response="200",
     *      description="Select phase for quiz"
     *     )
     * )
     */
    public function sendQuestionAnswer(QuizGame $game, ParamFetcher $paramFetcher): ResponseDTO
    {
        $this->denyAccessUnlessGranted(QuizGameVoter::ATTRIBUTE_PUT_ANSWER, $game);
        $answer = $paramFetcher->get('answer');

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new RuntimeException('User does not exists');
        }

        $this->quizGameService->putAnswer($game, $answer, $user);

        return Responser::wrapSuccess(true);
    }
}
