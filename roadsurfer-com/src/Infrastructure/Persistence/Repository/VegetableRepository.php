<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Infrastructure\Persistence\Entity\Vegetable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vegetable>
 */
class VegetableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vegetable::class);
    }

    /**
     * @return Vegetable[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('v')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Vegetable[]
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    public function persist(Vegetable $vegetable): void
    {
        $this->_em->persist($vegetable);
    }

    public function remove(Vegetable $vegetable): void
    {
        $this->_em->remove($vegetable);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
