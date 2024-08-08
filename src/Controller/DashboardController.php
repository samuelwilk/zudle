<?php

namespace App\Controller;

use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(private readonly GameService $gameService)
    {
    }
    #[Route('/', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        $startOfCurrentMonth = new \DateTime('first day of this month');
        $endOfCurrentMonth = new \DateTime('last day of this month');
        return $this->render('dashboard/index.html.twig', [
            'games' => $this->gameService->getGamesBetweenDates($startOfCurrentMonth, $endOfCurrentMonth),
        ]);
    }
}
