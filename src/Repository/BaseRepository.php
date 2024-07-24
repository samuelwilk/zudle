<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template T of object
 *
 * @template-extends ServiceEntityRepository<T>
 *
 * @extends ServiceEntityRepository<BaseRepository>
 */
abstract class BaseRepository extends ServiceEntityRepository
{
    private readonly string $entityClass;

    /**
     * Class BaseRepository.
     *
     * @param string $entityClass The class name of the entity this repository manages
     *
     * @psalm-param class-string<T> $entityClass
     */
    public function __construct(
        protected EntityManagerInterface $em,
        ManagerRegistry $registry,
        string $entityClass
    ) {
        parent::__construct($registry, $entityClass);
        $this->entityClass = $entityClass;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @throws \Exception
     */
    public function create(object $entity, bool $flush = false): object
    {
        try {
            $this->em->persist($entity);
            if ($flush) {
                $this->em->flush();
            }

            return $entity;
        } catch (\Exception $e) {
            throw new \Exception('Unable to create entity \n'.$e->getMessage(), 500);
        }
    }

    public function flush(): void
    {
        $this->em->flush();
    }

    /**
     * @throws \Exception
     */
    public function update(object $entity, bool $flush = false): void
    {
        try {
            $this->em->persist($entity);
            if ($flush) {
                $this->em->flush();
            }
        } catch (\Exception $exception) {
            $this->em->getConnection()->rollBack();
            throw $exception;
        }
    }

    /**
     * @param array<object> $entities
     *
     * @throws \Exception
     */
    public function updateAll(array $entities, bool $flush = false): void
    {
        try {
            foreach ($entities as $entity) {
                $this->em->persist($entity);
            }
            if ($flush) {
                $this->em->flush();
            }
        } catch (\Exception) {
            $this->em->getConnection()->rollBack();
            throw new \Exception('Unable to update entities', 500);
        }
    }

    /**
     * @throws \Exception
     */
    public function remove(object $entity, bool $flush = false): void
    {
        $this->em->remove($entity);
        if ($flush) {
            $this->em->flush();
        }
    }
}
