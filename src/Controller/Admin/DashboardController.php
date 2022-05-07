<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            ->setTitle('SummerProject');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::linkToCrud('Blog Posts', 'fa fa-tags', BlogPost::class),
            MenuItem::linkToCrud('Comments', 'fa fa-comments', Comment::class),
            MenuItem::linkToCrud('Users', 'fa fa-users', User::class)
                ->setPermission('ROLE_SUPERADMIN'),
            MenuItem::linkToCrud('Images', 'fa fa-images', Image::class),

            MenuItem::linkToLogout('Logout', 'fa fa-exit'),
        ];
    }
}
