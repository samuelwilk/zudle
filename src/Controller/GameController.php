<?php

namespace App\Controller;

use App\Entity\Game;
use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    public function __construct(private readonly GameService $gameService)
    {
    }

    #[Route('/game/{id}', name: 'app_game_show')]
    public function viewGame(Game $game): Response
    {
        try {
            $gameState = $this->gameService->getCurrentGameState($game);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to load the game: '.$e->getMessage());

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('game/view.html.twig', [
            'gameState' => $gameState,
        ]);
    }
}
