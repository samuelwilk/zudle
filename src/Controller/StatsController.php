<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatsController extends AbstractController
{
    #[Route('/stats', name: 'app_stats')]
    public function index(StatsService $statsService): Response
    {
        return $this->render('stats/index.html.twig', [
            'totalGames' => $statsService->getNumberOfGames(),
            'gamesTrend' => $statsService->getGamesTrend(),
            'totalGuesses' => $statsService->getNumberGuesses(),
            'guessTrend' => $statsService->getGuessTrend(),
            'gamesCompletedWithGuesses' => $statsService->getGamesCompletedWithGuesses(),
        ]);
    }

    #[Route('/stats/team/{teamId}', name: 'app_stats_team', requirements: ['teamId' => '\d+'])]
    public function teamStats(StatsService $statsService, ?Team $team = null): Response
    {
        return $this->render('stats/_teams-tab-data.frame.html.twig', [
            'teams' => $statsService->getTeamWinStats($team),
        ]);
    }

    #[Route('stats//user/{userId}', name: 'app_stats_user', requirements: ['userId' => '\d+'])]
    public function userStats(StatsService $statsService, ?User $user = null): Response
    {
        return $this->render('user/index.html.twig', [
            'GuessEvaluationPercentagesDto' => $statsService->calculateGuessEvaluationPercentages($user),
        ]);
    }
}
