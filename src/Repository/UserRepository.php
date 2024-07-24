<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\User;
use App\Enum\GameStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository
{
    public function __construct(EntityManagerInterface $em, ManagerRegistry $registry)
    {
        parent::__construct($em, $registry, User::class);
    }

    /**
     * @throws ORMException
     */
    public function getPlayerWinStats(?User $user = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id AS userId')
            ->addSelect('COUNT(game.id) AS gamesWon')
            ->join('u.guesses', 'guesses')
            ->join('guesses.game', 'game')
            ->where('game.status = :winStatus')
            ->setParameter('winStatus', GameStatusEnum::WON->value)
            ->groupBy('u.id');

        if (null !== $user) {
            $qb->andWhere('u.id = :userId')
                ->setParameter('userId', $user->getId());
        }

        $results = $qb->getQuery()->getResult();
        $structuredResults = [];

        foreach ($results as $result) {
            $user = $this->em->getReference(User::class, $result['userId']);
            $structuredResults[] = [
                'user' => $user,
                'gamesWon' => $result['gamesWon'],
            ];
        }

        return $structuredResults;
    }
}
