<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

abstract class AbstractZuCrudController extends AbstractCrudController
{
    // public function configureActions(Actions $actions): Actions
    // {
    //    return parent::configureActions($actions)
    //        ->setPermission(Action::BATCH_DELETE, User::ROLE_ADMIN)
    //        ->setPermission(Action::DELETE, User::ROLE_ADMIN)
    //        ->setPermission(Action::DETAIL, User::ROLE_ADMIN)
    //        ->setPermission(Action::EDIT, User::ROLE_ADMIN)
    //        ->setPermission(Action::INDEX, User::ROLE_ADMIN)
    //        ->setPermission(Action::NEW, User::ROLE_ADMIN);
    // }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            ->renderContentMaximized()

            // set this option if you prefer the sidebar (which contains the main menu)
            // to be displayed as a narrow column instead of the default expanded design
            ->renderSidebarMinimized()
        ;
    }
}
