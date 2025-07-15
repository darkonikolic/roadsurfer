<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Entity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vegetable>
 *
 * @method Vegetable|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vegetable|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vegetable[] findAll()
 * @method Vegetable[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VegetableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vegetable::class);
    }

    public function save(Vegetable $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function saveAndFlush(Vegetable $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(Vegetable $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function removeAndFlush(Vegetable $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @return Vegetable[] Returns an array of Vegetable objects
     */
    public function findBySearch(?string $search = null): array
    {
        $queryBuilder = $this->createQueryBuilder('v');

        if ($search) {
            $queryBuilder->andWhere('v.name LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        return $queryBuilder->orderBy('v.name', 'ASC')
                 ->getQuery()
                 ->getResult();
    }
}
