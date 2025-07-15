<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\External\Cache;

use App\Infrastructure\External\Cache\VegetableCacheService;
use App\Infrastructure\Persistence\Entity\Vegetable;
use App\Infrastructure\Persistence\Repository\VegetableRepository;
use App\Tests\Integration\AbstractIntegrationTestCase;
use Redis;

class VegetableCacheServiceIntegrationTest extends AbstractIntegrationTestCase
{
    private VegetableCacheService $vegetableCacheService;
    private VegetableRepository $vegetableRepository;
    private Redis $redis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vegetableRepository = new VegetableRepository(
            static::getContainer()->get('doctrine')
        );

        $this->redis                 = static::getContainer()->get('Redis');
        $this->vegetableCacheService = new VegetableCacheService(
            $this->redis,
            $this->vegetableRepository,
            1 // 1 second TTL
        );

        // Clean Redis before each test
        $this->cleanupRedis();
    }

    protected function tearDown(): void
    {
        // Clean Redis after each test
        $this->cleanupRedis();
        parent::tearDown();
    }

    private function cleanupRedis(): void
    {
        $keys = $this->redis->keys('vegetables:*');
        if (!empty($keys)) {
            $this->redis->del($keys);
        }
    }

    public function testCacheServiceWithRepositoryIntegration(): void
    {
        // Start transaction
        $this->entityManager->beginTransaction();

        try {
            // 1. Create and save entity via repository
            $testVegetable = new Vegetable();
            $testVegetable->setName('Test Carrot');
            $testVegetable->setQuantity(75.25);

            $this->vegetableRepository->persist($testVegetable);
            $this->vegetableRepository->flush();

            // Verify entity was saved with ID
            $this->assertNotNull($testVegetable->getId());
            $this->assertEquals('Test Carrot', $testVegetable->getName());
            $this->assertEqualsWithDelta(75.25, $testVegetable->getQuantity(), 0.01);

            // 2. Load entity via cache service (should hit repository first time)
            $cachedVegetables = $this->vegetableCacheService->findAll();

            // Verify cache service returned the same entity
            $this->assertGreaterThanOrEqual(1, count($cachedVegetables));

            $foundVegetable = null;
            foreach ($cachedVegetables as $vegetable) {
                if ($vegetable->getId() === $testVegetable->getId()) {
                    $foundVegetable = $vegetable;
                    break;
                }
            }

            $this->assertNotNull($foundVegetable, 'Test vegetable should be found in cache service results');
            $this->assertEquals($testVegetable->getId(), $foundVegetable->getId());
            $this->assertEquals($testVegetable->getName(), $foundVegetable->getName());
            $this->assertEqualsWithDelta($testVegetable->getQuantity(), $foundVegetable->getQuantity(), 0.01);

            // 3. Verify data exists in Redis cache
            $cacheKey   = $this->vegetableCacheService->getCacheKey(['vegetable', 'all']);
            $cachedData = $this->redis->get($cacheKey);
            $this->assertNotFalse($cachedData, 'Cache should contain data');

            $decodedData = json_decode($cachedData, true);
            $this->assertIsArray($decodedData);
            $this->assertGreaterThanOrEqual(1, count($decodedData));

            // Find our vegetable in cached data
            $foundInCache = false;
            foreach ($decodedData as $cachedVegetable) {
                if ($cachedVegetable['id'] === $testVegetable->getId()) {
                    $foundInCache = true;
                    $this->assertEquals($testVegetable->getName(), $cachedVegetable['name']);
                    $this->assertEqualsWithDelta($testVegetable->getQuantity(), $cachedVegetable['quantity'], 0.01);
                    break;
                }
            }
            $this->assertTrue($foundInCache, 'Vegetable should be found in Redis cache');

            // 6. Test findByName functionality (before update)
            $vegetablesByName = $this->vegetableCacheService->findByName('Test Carrot');
            $this->assertGreaterThanOrEqual(1, count($vegetablesByName));

            $foundByName = false;
            foreach ($vegetablesByName as $vegetable) {
                if ($vegetable->getId() === $testVegetable->getId()) {
                    $foundByName = true;
                    break;
                }
            }
            $this->assertTrue($foundByName, 'Vegetable should be found by name in cache');

            // 7. Update entity via repository (without invalidating cache)
            $testVegetable->setName('Updated Carrot');
            $testVegetable->setQuantity(125.50);

            $this->vegetableRepository->persist($testVegetable);
            $this->vegetableRepository->flush();

            // 8. Load again via cache service (should return old cached version)
            $cachedVegetablesAfterUpdate = $this->vegetableCacheService->findAll();

            $foundVegetableAfterUpdate = null;
            foreach ($cachedVegetablesAfterUpdate as $vegetable) {
                if ($vegetable->getId() === $testVegetable->getId()) {
                    $foundVegetableAfterUpdate = $vegetable;
                    break;
                }
            }

            $this->assertNotNull($foundVegetableAfterUpdate, 'Vegetable should still be found in cache');

            // Should return old cached version (not updated)
            $this->assertEquals('Test Carrot', $foundVegetableAfterUpdate->getName());
            $this->assertEqualsWithDelta(75.25, $foundVegetableAfterUpdate->getQuantity(), 0.01);

            // Verify repository has updated data
            $freshVegetable = $this->vegetableRepository->find($testVegetable->getId());
            $this->assertEquals('Updated Carrot', $freshVegetable->getName());
            $this->assertEqualsWithDelta(125.50, $freshVegetable->getQuantity(), 0.01);

            // 7. Test findByName after update (should return cached version)
            $vegetablesByNameAfterUpdate = $this->vegetableCacheService->findByName('Test Carrot');
            $this->assertGreaterThanOrEqual(1, count($vegetablesByNameAfterUpdate));

            $foundByNameAfterUpdate = false;
            foreach ($vegetablesByNameAfterUpdate as $vegetable) {
                if ($vegetable->getId() === $testVegetable->getId()) {
                    $foundByNameAfterUpdate = true;
                    break;
                }
            }
            $this->assertTrue($foundByNameAfterUpdate, 'Vegetable should still be found by name in cache after update');

            // Commit transaction
            $this->entityManager->commit();

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function testCacheInvalidation(): void
    {
        // Start transaction
        $this->entityManager->beginTransaction();

        try {
            // Create and save entity
            $testVegetable = new Vegetable();
            $testVegetable->setName('Cache Test Carrot');
            $testVegetable->setQuantity(50.0);

            $this->vegetableRepository->persist($testVegetable);
            $this->vegetableRepository->flush();

            // Load via cache service (populates cache)
            $this->vegetableCacheService->findAll();

            // Verify cache exists
            $cacheKey = $this->vegetableCacheService->getCacheKey(['vegetable', 'all']);
            $this->assertNotFalse($this->redis->get($cacheKey), 'Cache should exist');

            // Invalidate cache
            $this->vegetableCacheService->invalidateCache();

            // Verify cache is cleared
            $this->assertFalse($this->redis->get($cacheKey), 'Cache should be cleared after invalidation');

            // Load again (should hit repository and repopulate cache)
            $cachedVegetables = $this->vegetableCacheService->findAll();
            $this->assertGreaterThanOrEqual(1, count($cachedVegetables));

            // Verify cache is repopulated
            $this->assertNotFalse($this->redis->get($cacheKey), 'Cache should be repopulated');

            // Commit transaction
            $this->entityManager->commit();

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->entityManager->rollback();
            throw $e;
        }
    }
}
