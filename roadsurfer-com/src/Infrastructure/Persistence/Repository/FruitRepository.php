<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Infrastructure\Persistence\Entity\Fruit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fruit>
 *
 * @method Fruit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fruit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fruit[] findAll()
 * @method Fruit[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Fruit[]
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function persist(Fruit $fruit): void
    {
        $this->_em->persist($fruit);
    }

    public function remove(Fruit $fruit): void
    {
        $this->_em->remove($fruit);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
