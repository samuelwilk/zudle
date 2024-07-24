<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Alert extends BaseComponent
{
    public string $id;
    public string $type;
    public string $message;
}
