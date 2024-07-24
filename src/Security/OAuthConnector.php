<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class OAuthConnector implements AccountConnectorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private array $properties
    ) {
    }

    public function connect(UserInterface $user, UserResponseInterface $response): void
    {
        if (!isset($this->properties[$response->getResourceOwner()->getName()])) {
            return;
        }

        $property = new PropertyAccessor();
        $property->setValue($user, $this->properties[$response->getResourceOwner()->getName()], $response->getUserIdentifier());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
