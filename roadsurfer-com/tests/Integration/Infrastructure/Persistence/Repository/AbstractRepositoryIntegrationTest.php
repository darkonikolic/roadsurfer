<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Persistence\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractRepositoryIntegrationTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        
        // Start transaction for test isolation
        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        // Rollback transaction to clean up test data
        if ($this->entityManager->isOpen()) {
            $this->entityManager->rollback();
        }
        
        parent::tearDown();
    }

    /**
     * Persist and flush entity to database
     */
    protected function persistAndFlush(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Find entity by ID
     * 
     * @param string $entityClass
     * @param int $id
     *
     * @return object|null
     */
    protected function findById(string $entityClass, int $id): ?object
    {
        // @phpstan-ignore-next-line
        return $this->entityManager->find($entityClass, $id);
    }

    /**
     * Assert entity was persisted with ID
     */
    protected function assertEntityPersisted(object $entity): void
    {
        $this->assertNotNull($entity->getId(), 'Entity should have an ID after persistence');
    }

    /**
     * Assert entity can be retrieved from database
     */
    protected function assertEntityRetrievable(object $entity, string $entityClass): void
    {
        $retrieved = $this->findById($entityClass, $entity->getId());
        $this->assertNotNull($retrieved, 'Entity should be retrievable from database');
        $this->assertEquals($entity->getId(), $retrieved->getId(), 'Retrieved entity should have same ID');
    }
}
