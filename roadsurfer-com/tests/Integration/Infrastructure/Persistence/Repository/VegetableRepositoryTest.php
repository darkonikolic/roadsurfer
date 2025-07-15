<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Persistence\Repository;

use App\Infrastructure\Persistence\Entity\Vegetable;
use App\Infrastructure\Persistence\Repository\VegetableRepository;
use App\Tests\Integration\Infrastructure\Persistence\Repository\AbstractRepositoryIntegrationTest;

class VegetableRepositoryTest extends AbstractRepositoryIntegrationTest
{
    private VegetableRepository $vegetableRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vegetableRepository = new VegetableRepository(
            static::getContainer()->get('doctrine')
        );
    }

    public function testVegetableRepositoryWriteAndRead(): void
    {
        // Create a test vegetable entity
        $testVegetable = new Vegetable();
        $testVegetable->setName('Test Carrot');
        $testVegetable->setQuantity(75.25);

        // Write to database
        $this->persistAndFlush($testVegetable);

        // Verify the entity was saved with an ID
        $this->assertEntityPersisted($testVegetable);
        $this->assertEquals('Test Carrot', $testVegetable->getName());
        $this->assertEqualsWithDelta(75.25, $testVegetable->getQuantity(), 0.01);
        $this->assertNotNull($testVegetable->getCreatedAt());
        $this->assertNotNull($testVegetable->getUpdatedAt());

        // Read from database using repository
        $foundVegetable = $this->vegetableRepository->find($testVegetable->getId());

        // Verify the entity was read correctly
        $this->assertEntityRetrievable($testVegetable, Vegetable::class);
        $this->assertEquals('Test Carrot', $foundVegetable->getName());
        $this->assertEqualsWithDelta(75.25, $foundVegetable->getQuantity(), 0.01);
        $this->assertEquals($testVegetable->getCreatedAt()->format('Y-m-d H:i:s'), $foundVegetable->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals($testVegetable->getUpdatedAt()->format('Y-m-d H:i:s'), $foundVegetable->getUpdatedAt()->format('Y-m-d H:i:s'));

        // Test finding by name
        $vegetablesByName = $this->vegetableRepository->findByName('Test Carrot');
        $this->assertGreaterThanOrEqual(1, count($vegetablesByName));

        $foundByName = false;
        foreach ($vegetablesByName as $vegetable) {
            if ($vegetable->getId() === $testVegetable->getId()) {
                $foundByName = true;
                break;
            }
        }
        $this->assertTrue($foundByName, 'Test vegetable should be found by name');

        // Test finding all vegetables
        $allVegetables = $this->vegetableRepository->findAll();
        $this->assertGreaterThanOrEqual(1, count($allVegetables));

        $foundInAll = false;
        foreach ($allVegetables as $vegetable) {
            if ($vegetable->getId() === $testVegetable->getId()) {
                $foundInAll = true;
                break;
            }
        }
        $this->assertTrue($foundInAll, 'Test vegetable should be found in all vegetables');
    }

    public function testSaveAndRemove(): void
    {
        // Test save method
        $testVegetable = new Vegetable();
        $testVegetable->setName('Test Save Vegetable');
        $testVegetable->setQuantity(50.0);

        // Use repository persist and flush methods
        $this->vegetableRepository->persist($testVegetable);
        $this->vegetableRepository->flush();

        // Extract ID for verification
        $vegetableId = $testVegetable->getId();

        // Verify it was saved using EntityManager directly
        $savedVegetable = $this->entityManager->find(Vegetable::class, $vegetableId);
        $this->assertNotNull($savedVegetable, 'Saved vegetable should be retrievable');
        $this->assertEquals('Test Save Vegetable', $savedVegetable->getName());

        // Test remove method
        $this->vegetableRepository->remove($testVegetable);
        $this->vegetableRepository->flush();

        // Verify it was removed using EntityManager directly
        $removedVegetable = $this->entityManager->find(Vegetable::class, $vegetableId);
        $this->assertNull($removedVegetable, 'Removed vegetable should not be retrievable');
    }
}
