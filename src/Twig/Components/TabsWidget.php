<?php

namespace App\Twig\Components;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class TabsWidget extends AbstractController
{
    public function __construct(private readonly StatsService $statsService)
    {
    }

    public string $title = 'Wins';

    public array $tabs = [
        [
            'name' => 'Games',
            'id' => 'games',
        ],
        [
            'name' => 'Players',
            'id' => 'players',
        ],
    ];

    public function teamStats(?int $teamId = null): array
    {
        return $this->statsService->getTeamWinStats($teamId);
        // return $this->render('stats/_teams-tab-data.frame.html.twig', [
        //    'teams' => $this->statsService->getTeamWinStats($team),
        // ]);
    }

    public function playerStats(?int $userId = null): array
    {
        return $this->statsService->getPlayerWinStats($userId);
        // return $this->render('stats/_teams-tab-data.frame.html.twig', [
        //    'teams' => $this->statsService->getTeamWinStats($team),
        // ]);
    }
}
