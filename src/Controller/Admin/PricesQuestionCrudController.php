<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Game\Quiz\Phase\Prices\PricesQuestion;
use App\Form\Game\Quiz\Phase\Prices\PricesAnswerType;
use App\Form\Game\Quiz\Phase\Questions\QuestionsAnswerType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PricesQuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PricesQuestion::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['question'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(30)
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('question'),
            BooleanField::new('enabled'),
            CollectionField::new('answers')->setEntryType(PricesAnswerType::class)->setFormTypeOptions(['by_reference' => false]),
        ];
    }

    public function edit(AdminContext $context)
    {
        return parent::edit($context);
    }
}
