<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Persistence\Repository;

use App\Infrastructure\Persistence\Entity\Fruit;
use App\Infrastructure\Persistence\Repository\FruitRepository;
use App\Tests\Integration\Infrastructure\Persistence\Repository\AbstractRepositoryIntegrationTest;

class FruitRepositoryTest extends AbstractRepositoryIntegrationTest
{
    private FruitRepository $fruitRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fruitRepository = new FruitRepository(
            static::getContainer()->get('doctrine')
        );
    }

    public function testFruitRepositoryWriteAndRead(): void
    {
        // Create a test fruit entity
        $testFruit = new Fruit();
        $testFruit->setName('Test Apple');
        $testFruit->setQuantity(150.50);

        // Write to database
        $this->persistAndFlush($testFruit);

        // Verify the entity was saved with an ID
        $this->assertEntityPersisted($testFruit);
        $this->assertEquals('Test Apple', $testFruit->getName());
        $this->assertEqualsWithDelta(150.50, $testFruit->getQuantity(), 0.01);
        $this->assertNotNull($testFruit->getCreatedAt());
        $this->assertNotNull($testFruit->getUpdatedAt());

        // Read from database using repository
        $foundFruit = $this->fruitRepository->find($testFruit->getId());

        // Verify the entity was read correctly
        $this->assertEntityRetrievable($testFruit, Fruit::class);
        $this->assertEquals('Test Apple', $foundFruit->getName());
        $this->assertEqualsWithDelta(150.50, $foundFruit->getQuantity(), 0.01);
        $this->assertEquals($testFruit->getCreatedAt()->format('Y-m-d H:i:s'), $foundFruit->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals($testFruit->getUpdatedAt()->format('Y-m-d H:i:s'), $foundFruit->getUpdatedAt()->format('Y-m-d H:i:s'));

        // Test finding by name
        $fruitsByName = $this->fruitRepository->findByName('Test Apple');
        $this->assertGreaterThanOrEqual(1, count($fruitsByName));

        $foundByName = false;
        foreach ($fruitsByName as $fruit) {
            if ($fruit->getId() === $testFruit->getId()) {
                $foundByName = true;
                break;
            }
        }
        $this->assertTrue($foundByName, 'Test fruit should be found by name');
    }

    public function testSaveAndRemove(): void
    {
        // Test save method
        $testFruit = new Fruit();
        $testFruit->setName('Test Save Fruit');
        $testFruit->setQuantity(100.0);

        // Use repository persist and flush methods
        $this->fruitRepository->persist($testFruit);
        $this->fruitRepository->flush();

        // Extract ID for verification
        $fruitId = $testFruit->getId();

        // Verify it was saved using EntityManager directly
        $savedFruit = $this->entityManager->find(Fruit::class, $fruitId);
        $this->assertNotNull($savedFruit, 'Saved fruit should be retrievable');
        $this->assertEquals('Test Save Fruit', $savedFruit->getName());

        // Test remove method
        $this->fruitRepository->remove($testFruit);
        $this->fruitRepository->flush();

        // Verify it was removed using EntityManager directly
        $removedFruit = $this->entityManager->find(Fruit::class, $fruitId);
        $this->assertNull($removedFruit, 'Removed fruit should not be retrievable');
    }
}
