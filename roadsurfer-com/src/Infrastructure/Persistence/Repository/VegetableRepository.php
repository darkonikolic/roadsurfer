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
            ->select('v')
            ->from(Vegetable::class, 'v')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Vegetable[]
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('v')
            ->select('v')
            ->from(Vegetable::class, 'v')
            ->where('v.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Vegetable[]
     */
    public function findByQuantityRange(float $minQuantity, float $maxQuantity): array
    {
        return $this->createQueryBuilder('v')
            ->select('v')
            ->from(Vegetable::class, 'v')
            ->where('v.quantity BETWEEN :minQuantity AND :maxQuantity')
            ->setParameter('minQuantity', $minQuantity)
            ->setParameter('maxQuantity', $maxQuantity)
            ->getQuery()
            ->getResult();
    }

    public function save(Vegetable $vegetable): void
    {
        $this->_em->persist($vegetable);
        $this->_em->flush();
    }

    public function remove(Vegetable $vegetable): void
    {
        $this->_em->remove($vegetable);
        $this->_em->flush();
    }
}
