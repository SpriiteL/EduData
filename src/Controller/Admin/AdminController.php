<?php

namespace App\Controller\Admin;


use App\Entity\User;
use App\Entity\Etablishment;
use App\Entity\Inventory;
use App\Entity\Room;
use App\Entity\ExportLogs;
use App\Entity\ImportLogs;
use App\Entity\Statistics;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();
        
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);$routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);

        // if(in_array('ROLE_ADMIN', $this->getUser()->getRoles(), false)) {
        //     return new RedirectResponse($this->urlGenerator->generate('login'));
        // }
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EduData');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linktoRoute('Back to the website', 'fa-solid fa-gear', 'app_dashboard');
        yield MenuItem::linkToCrud('User', 'fa-solid fa-user', User::class);
        yield MenuItem::linkToCrud('Etablishment', 'fa-solid fa-building-columns', Etablishment::class);
        yield MenuItem::linkToCrud('Inventory', 'fa-solid fa-cart-flatbed', Inventory::class);
        yield MenuItem::linkToCrud('Room', 'fa-solid fa-chair', Room::class);
        yield MenuItem::linkToCrud('ImportsLogs', 'fa-solid fa-file-import', ImportLogs::class);
        yield MenuItem::linkToCrud('ExportLogs', 'fa-solid fa-file-export', ExportLogs::class);
        yield MenuItem::linkToCrud('Statistics', 'fa-solid fa-chart-simple', Statistics::class);
    }
}
