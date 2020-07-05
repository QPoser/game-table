<?php
declare(strict_types=1);

namespace App\Controller\Api\Game;

use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\QuizGame;
use App\Security\Voter\QuizGameVoter;
use App\Services\Game\Quiz\QuizGameService;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/game/quiz", name="api.game.quiz")
 */
class QuizGameController extends AbstractController
{
    private QuizGameService $quizGameService;

    public function __construct(QuizGameService $quizGameService)
    {
        $this->quizGameService = $quizGameService;
    }

    /**
     * @Route("/phases", name=".phases", methods={"GET"})
     * @Rest\View(serializerGroups={"Api"})
     * @SWG\Post(
     *     tags={"Quiz game"},
     *     @SWG\Response(
     *      response="200",
     *      description="Get available quiz phases"
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
     *      description="Select phase for quiz",
     *      @Model(type=QuizGame::class, groups={"Api", "GameMessages"})
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
     * @Route("/{id}/answer", name=".phase.select", methods={"POST"})
     * @Rest\View(serializerGroups={"Api"})
     * @Rest\RequestParam(name="answer", requirements="\w+", nullable=false, strict=true, description="Answer")
     * @SWG\Post(
     *     tags={"Quiz game"},
     *     @SWG\Response(
     *      response="200",
     *      description="Select phase for quiz",
     *      @Model(type=QuizGame::class, groups={"Api", "GameMessages"})
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
