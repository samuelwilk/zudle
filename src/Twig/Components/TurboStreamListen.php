<?php

namespace App\Twig\Components;

use App\Validator\Constraints\IsValidClassName;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class TurboStreamListen
{
    #[IsValidClassName]
    public array $entities = [
        'App\\Entity\\Game',
    ];
}
