<?php

declare(strict_types=1);

namespace App\Controller\Game;

use App\Entity\Game\Game;
use App\Security\Voter\GameVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/games", name="app.games")
 */
final class GameController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="")
     */
    public function index(): Response
    {
        [$games, $pagination] = $this
            ->em
            ->getRepository(Game::class)
            ->getGamesWithPagination(20, 0);

        return $this->render('game/game/index.html.twig', compact('games'));
    }

    /**
     * @Route("/{id}", name=".game.visit")
     */
    public function gameVisit(Game $game): Response
    {
        $this->denyAccessUnlessGranted(GameVoter::ATTRIBUTE_VISIT, $game);

        return $this->render('game/game/game.html.twig', compact('game'));
    }
}
