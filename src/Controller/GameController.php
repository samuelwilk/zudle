<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Guess;
use App\Form\GuessType;
use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    public function __construct(private readonly GameService $gameService)
    {
    }

    #[Route('/game/{id}', name: 'app_game_show')]
    public function viewGame(Request $request, Game $game, HubInterface $hub): Response
    {
        try {
            $gameState = $this->gameService->getCurrentGameState($game);
            $form = $this->createForm(GuessType::class, new Guess(), [
                'length' => $game->getWordLength(),
            ]);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Guess $guess */
                $guess = $form->getData();
                $game = $this->gameService->makeGuess($game, $guess->getGuess());

                $this->publishMakeGuessUpdates($game, $hub);

                return $this->redirectToRoute('app_game_show', ['id' => $game->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to load the game: '.$e->getMessage());

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('game/view.html.twig', [
            'form' => $form->createView(),
            'gameState' => $gameState,
        ]);
    }

    private function publishMakeGuessUpdates(Game $game, HubInterface $hub): void
    {
        try {
            $hub->publish(new Update(
                'game_state',
                $this->renderView('game/game_state.stream.html.twig', [
                    'gameState' => $this->gameService->getCurrentGameState($game),
                ])
            ));

            //$hub->publish(new Update(
            //    'game_calendar',
            //    $this->renderView('dashboard/calendar/game_calendar.stream.html.twig', [
            //        'games' => $this->gameService->getGames(),
            //    ])
            //));
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to publish updates: '.$e->getMessage());
        }
    }
}
