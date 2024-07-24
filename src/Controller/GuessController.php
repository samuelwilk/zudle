<?php

namespace App\Controller;

use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GuessController extends AbstractController
{
    ///**
    // * @throws \Exception
    // */
    //#[Route('/guess', name: 'app_guess')]
    //public function makeGuess(Request $request, GameService $gameService): Response
    //{
    //    $data = json_decode($request->getContent(), true);
    //    $game = $gameService->getGame($data['gameId']);
    //
    //    $gameService->makeGuess($game, $data['guess']);
    //
    //    return $this->redirectToRoute('app_game_show', ['id' => $game->getId()]);
    //}
}
