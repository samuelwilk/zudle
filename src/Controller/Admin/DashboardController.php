<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use App\Entity\GameSettings;
use App\Entity\Team;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SubMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('zudle');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Games', 'fas fa-gamepad', Game::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Teams', 'fas fa-user-group', Team::class);
    }

}
