<?php

namespace App\Twig\Forms;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class BaseForm extends AbstractController
{
    public function __construct(private readonly HubInterface $hub)
    {
    }

    protected function publishUpdate(string $target, string $content): void
    {
        $this->hub->publish(new Update($target, $content));
    }
}
