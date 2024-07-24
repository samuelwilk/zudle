<?php

namespace App\Repository;

use App\Entity\Team;
use App\Enum\GameStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends BaseRepository<Team>
 */
class TeamRepository extends BaseRepository
{
    public function __construct(EntityManagerInterface $em, ManagerRegistry $registry)
    {
        parent::__construct($em, $registry, Team::class);
    }

    /**
     * @throws ORMException
     */
    public function getTeamWinStats(?Team $team = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.id AS teamId')
            ->addSelect('COUNT(g.id) AS gamesWon')
            ->join('t.users', 'u')
            ->join('u.guesses', 'gu')
            ->join('gu.game', 'g')
            ->where('g.status = :winStatus')
            ->setParameter('winStatus', GameStatusEnum::WON->value)
            ->groupBy('t.id');

        if (null !== $team) {
            $qb->andWhere('t.id = :teamId')
                ->setParameter('teamId', $team->getId());
        }

        $results = $qb->getQuery()->getResult();
        $structuredResults = [];

        foreach ($results as $result) {
            $team = $this->em->getReference(Team::class, $result['teamId']);
            $structuredResults[] = [
                'team' => $team,
                'gamesWon' => $result['gamesWon'],
            ];
        }

        return $structuredResults;
    }
}
