<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends BaseRepository<Game>
 */
class GameRepository extends BaseRepository
{
    public function __construct(EntityManagerInterface $em, ManagerRegistry $registry)
    {
        parent::__construct($em, $registry, Game::class);
    }

    public function findGamesCreatedThisMonth()
    {
        $qb = $this->createQueryBuilder('g');
        $startOfMonth = new \DateTime('first day of this month 00:00:00');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');

        $qb->where('g.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth);

        return $qb->getQuery()->getResult();
    }

    public function findByCreationDateRange(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('g');
        $qb->where('g.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        return $qb->getQuery()->getResult();
    }
}
