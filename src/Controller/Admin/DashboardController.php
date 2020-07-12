<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Game\Quiz\Phase\Questions\QuestionsQuestion;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Game Table Admin')
            ->setTextDirection('ltr');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linktoDashboard('Dashboard', 'fa-home'),

            MenuItem::section('Games'),
            MenuItem::subMenu('Questions', 'fa fa-question')->setSubItems([
                MenuItem::linkToCrud('Quiz questions', 'fa fa-question', QuestionsQuestion::class),
            ]),

        ];
    }
}