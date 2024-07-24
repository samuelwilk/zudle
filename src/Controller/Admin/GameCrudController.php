<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use App\Enum\GameStatusEnum;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GameCrudController extends AbstractZuCrudController
{
    public static function getEntityFqcn(): string
    {
        return Game::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('word'),
            ChoiceField::new('status')->setChoices([
                GameStatusEnum::IN_PROGRESS->beautifyEnumKey() => GameStatusEnum::IN_PROGRESS,
                GameStatusEnum::WON->beautifyEnumKey() => GameStatusEnum::WON,
                GameStatusEnum::LOST->beautifyEnumKey() => GameStatusEnum::LOST,
            ])->hideWhenCreating(),
            NumberField::new('attempts')->onlyOnIndex(),
            CollectionField::new('guesses')->allowAdd(false)->hideWhenCreating(),
            NumberField::new('maxAttempts'),
        ];
    }
}
