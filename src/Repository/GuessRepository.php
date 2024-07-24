<?php

namespace App\Repository;

use App\Entity\Guess;
use App\Entity\User;
use App\Enum\GameStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends BaseRepository<Guess>
 */
class GuessRepository extends BaseRepository
{
    public function __construct(EntityManagerInterface $em, ManagerRegistry $registry)
    {
        parent::__construct($em, $registry, Guess::class);
    }

    /**
     * @return Guess[]
     */
    public function findGuessesByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('guess')
            ->join('guess.user', 'user')
            ->andWhere('user.id = :userId')
            ->setParameter('userId', $user->getId())
        ;

        return $qb->getQuery()->getResult();
    }
}
