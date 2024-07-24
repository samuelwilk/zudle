<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class StatsController extends AbstractController
{
    #[Route('/stats', name: 'app_stats')]
    public function index(ChartBuilderInterface $chartBuilder, StatsService $statsService): Response
    {
        // Step 1: Fetch data
        $gamesOverTime = $statsService->getGamesGroupedByDayForCurrentMonth();

        // Step 2: Prepare data for the chart
        $labels = [];
        $data = [];
        foreach ($gamesOverTime as $day => $games) {
            $labels[] = $day;
            $data[] = count($games);
        }

        // Step 3: Update the chart creation code
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'User Participation Over Time',
                    'backgroundColor' => 'rgb(75, 192, 192)',
                    'borderColor' => 'rgb(75, 192, 192)',
                    'data' => $data,
                    'fill' => false,
                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    // Adjust the max value if necessary
                    'suggestedMax' => max($data) + 10,
                ],
            ],
        ]);

        return $this->render('stats/index.html.twig', [
            'chart' => $chart,
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
