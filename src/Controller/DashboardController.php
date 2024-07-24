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
        return $this->render('dashboard/index.html.twig', [
            'games' => $this->gameService->getGames(),
        ]);
    }
}
