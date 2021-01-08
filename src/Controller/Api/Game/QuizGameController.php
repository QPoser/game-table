<?php

declare(strict_types=1);

namespace App\Controller\Api\Game;

use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\QuizGame;
use App\Security\Voter\QuizGameVoter;
use App\Services\Game\Quiz\QuizGameService;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
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
     *
     *           )
     *      )
     *     )
     * )
     */
    public function getPhases(): array
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
    public function selectPhase(QuizGame $game, ParamFetcher $paramFetcher): array
    {
        $this->denyAccessUnlessGranted(QuizGameVoter::ATTRIBUTE_SELECT_PHASE, $game);

        $phaseType = $paramFetcher->get('phase_type');
        $this->quizGameService->addPhase($game, $phaseType, $this->getUser());

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
    public function sendQuestionAnswer(QuizGame $game, ParamFetcher $paramFetcher): array
    {
        $this->denyAccessUnlessGranted(QuizGameVoter::ATTRIBUTE_PUT_ANSWER, $game);
        $answer = $paramFetcher->get('answer');

        $this->quizGameService->putAnswer($game, $answer, $this->getUser());

        return Responser::wrapSuccess(true);
    }
}
