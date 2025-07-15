<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\External\Cache;

use App\Infrastructure\External\Cache\FruitCacheService;
use App\Infrastructure\Persistence\Entity\Fruit;
use App\Infrastructure\Persistence\Repository\FruitRepository;
use App\Tests\Integration\AbstractIntegrationTestCase;
use Redis;

class FruitCacheServiceIntegrationTest extends AbstractIntegrationTestCase
{
    private FruitCacheService $fruitCacheService;
    private FruitRepository $fruitRepository;
    private Redis $redis;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fruitRepository = new FruitRepository(
            static::getContainer()->get('doctrine')
        );
        
        $this->redis             = static::getContainer()->get('Redis');
        $this->fruitCacheService = new FruitCacheService(
            $this->redis,
            $this->fruitRepository,
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
        $keys = $this->redis->keys('fruits:*');
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
            $testFruit = new Fruit();
            $testFruit->setName('Test Apple');
            $testFruit->setQuantity(150.50);
            
            $this->fruitRepository->persist($testFruit);
            $this->fruitRepository->flush();
            
            // Verify entity was saved with ID
            $this->assertNotNull($testFruit->getId());
            $this->assertEquals('Test Apple', $testFruit->getName());
            $this->assertEqualsWithDelta(150.50, $testFruit->getQuantity(), 0.01);
            
            // 2. Load entity via cache service (should hit repository first time)
            $cachedFruits = $this->fruitCacheService->findAll();
            
            // Verify cache service returned the same entity
            $this->assertGreaterThanOrEqual(1, count($cachedFruits));
            
            $foundFruit = null;
            foreach ($cachedFruits as $fruit) {
                if ($fruit->getId() === $testFruit->getId()) {
                    $foundFruit = $fruit;
                    break;
                }
            }
            
            $this->assertNotNull($foundFruit, 'Test fruit should be found in cache service results');
            $this->assertEquals($testFruit->getId(), $foundFruit->getId());
            $this->assertEquals($testFruit->getName(), $foundFruit->getName());
            $this->assertEqualsWithDelta($testFruit->getQuantity(), $foundFruit->getQuantity(), 0.01);
            
            // 3. Verify data exists in Redis cache
            $cacheKey   = $this->fruitCacheService->getCacheKey(['fruit', 'all']);
            $cachedData = $this->redis->get($cacheKey);
            $this->assertNotFalse($cachedData, 'Cache should contain data');
            
            $decodedData = json_decode($cachedData, true);
            $this->assertIsArray($decodedData);
            $this->assertGreaterThanOrEqual(1, count($decodedData));
            
            // Find our fruit in cached data
            $foundInCache = false;
            foreach ($decodedData as $cachedFruit) {
                if ($cachedFruit['id'] === $testFruit->getId()) {
                    $foundInCache = true;
                    $this->assertEquals($testFruit->getName(), $cachedFruit['name']);
                    $this->assertEqualsWithDelta($testFruit->getQuantity(), $cachedFruit['quantity'], 0.01);
                    break;
                }
            }
            $this->assertTrue($foundInCache, 'Fruit should be found in Redis cache');
            
            // 6. Test findByName functionality (before update)
            $fruitsByName = $this->fruitCacheService->findByName('Test Apple');
            $this->assertGreaterThanOrEqual(1, count($fruitsByName));
            
            $foundByName = false;
            foreach ($fruitsByName as $fruit) {
                if ($fruit->getId() === $testFruit->getId()) {
                    $foundByName = true;
                    break;
                }
            }
            $this->assertTrue($foundByName, 'Fruit should be found by name in cache');
            
            // 7. Update entity via repository (without invalidating cache)
            $testFruit->setName('Updated Apple');
            $testFruit->setQuantity(200.75);
            
            $this->fruitRepository->persist($testFruit);
            $this->fruitRepository->flush();
            
            // 8. Load again via cache service (should return old cached version)
            $cachedFruitsAfterUpdate = $this->fruitCacheService->findAll();
            
            $foundFruitAfterUpdate = null;
            foreach ($cachedFruitsAfterUpdate as $fruit) {
                if ($fruit->getId() === $testFruit->getId()) {
                    $foundFruitAfterUpdate = $fruit;
                    break;
                }
            }
            
            $this->assertNotNull($foundFruitAfterUpdate, 'Fruit should still be found in cache');
            
            // Should return old cached version (not updated)
            $this->assertEquals('Test Apple', $foundFruitAfterUpdate->getName());
            $this->assertEqualsWithDelta(150.50, $foundFruitAfterUpdate->getQuantity(), 0.01);
            
            // Verify repository has updated data
            $freshFruit = $this->fruitRepository->find($testFruit->getId());
            $this->assertEquals('Updated Apple', $freshFruit->getName());
            $this->assertEqualsWithDelta(200.75, $freshFruit->getQuantity(), 0.01);
            
            // 9. Test findByName after update (should return cached version)
            $fruitsByNameAfterUpdate = $this->fruitCacheService->findByName('Test Apple');
            $this->assertGreaterThanOrEqual(1, count($fruitsByNameAfterUpdate));
            
            $foundByNameAfterUpdate = false;
            foreach ($fruitsByNameAfterUpdate as $fruit) {
                if ($fruit->getId() === $testFruit->getId()) {
                    $foundByNameAfterUpdate = true;
                    break;
                }
            }
            $this->assertTrue($foundByNameAfterUpdate, 'Fruit should still be found by name in cache after update');
            
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
            $testFruit = new Fruit();
            $testFruit->setName('Cache Test Apple');
            $testFruit->setQuantity(100.0);
            
            $this->fruitRepository->persist($testFruit);
            $this->fruitRepository->flush();
            
            // Load via cache service (populates cache)
            $this->fruitCacheService->findAll();
            
            // Verify cache exists
            $cacheKey = $this->fruitCacheService->getCacheKey(['fruit', 'all']);
            $this->assertNotFalse($this->redis->get($cacheKey), 'Cache should exist');
            
            // Invalidate cache
            $this->fruitCacheService->invalidateCache();
            
            // Verify cache is cleared
            $this->assertFalse($this->redis->get($cacheKey), 'Cache should be cleared after invalidation');
            
            // Load again (should hit repository and repopulate cache)
            $cachedFruits = $this->fruitCacheService->findAll();
            $this->assertGreaterThanOrEqual(1, count($cachedFruits));
            
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
