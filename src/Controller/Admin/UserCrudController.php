<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Etablishment;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setFormTypeOption('mapped', false),
            TextField::new('username'),
            TextField::new('username'),
            TextField::new('email'),
            TextField::new('firstname'),
            TextField::new('lastname'),
            TextField::new('password'),
            ChoiceField::new('roles')->setChoices(['USER' => 'ROLE_USER', 'ADMIN' => 'ROLE_ADMIN', 'RESPONSABLE' => 'ROLE_RESPONSABLE']),
            AssociationField::new('etablishment','etablishment'),
        ];
    }
}
