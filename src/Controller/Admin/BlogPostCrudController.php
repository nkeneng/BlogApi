<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Form\ImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BlogPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        $views = [
            Field::new('id')->onlyOnIndex(),
            Field::new('title'),
            TextEditorField::new('content'),
            CollectionField::new('images')->setEntryType(ImageType::class)->onlyOnForms(),
            DateTimeField::new('published')->hideOnForm(),
            CollectionField::new('images')
                ->setEntryType(VichImageType::class)
//                ->setFormType(ImageType::class)
                ->hideOnIndex()
            ,
            AssociationField::new('author')->hideOnForm()
        ];
        if ($pageName === Crud::PAGE_DETAIL) {
            $views[] = AssociationField::new('comments')->setTemplatePath('easy_admin/comments.html.twig')->hideOnForm();
        }else{
            $views[] = AssociationField::new('comments')->onlyOnForms();
        }

        return $views;
    }

    public function configureActions(Actions $actions): Actions
    {
        // * to add an icon
//        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
//        return $action->setIcon('fa fa-file-alt')->setLabel(false);
//    })
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

}
