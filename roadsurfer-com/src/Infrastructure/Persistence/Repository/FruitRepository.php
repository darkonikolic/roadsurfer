<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Infrastructure\Persistence\Entity\Fruit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fruit>
 */
class FruitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fruit::class);
    }

    /**
     * @return Fruit[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('f')
            ->select('f')
            ->from(Fruit::class, 'f')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Fruit[]
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('f')
            ->select('f')
            ->from(Fruit::class, 'f')
            ->where('f.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Fruit[]
     */
    public function findByQuantityRange(float $minQuantity, float $maxQuantity): array
    {
        return $this->createQueryBuilder('f')
            ->select('f')
            ->from(Fruit::class, 'f')
            ->where('f.quantity BETWEEN :minQuantity AND :maxQuantity')
            ->setParameter('minQuantity', $minQuantity)
            ->setParameter('maxQuantity', $maxQuantity)
            ->getQuery()
            ->getResult();
    }

    public function save(Fruit $fruit): void
    {
        $this->_em->persist($fruit);
        $this->_em->flush();
    }

    public function remove(Fruit $fruit): void
    {
        $this->_em->remove($fruit);
        $this->_em->flush();
    }
}
