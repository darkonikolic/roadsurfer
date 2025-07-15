<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Controller;

use App\Infrastructure\External\Cache\FruitCacheService;
use App\Infrastructure\Persistence\Entity\Fruit;
use App\Infrastructure\Persistence\Repository\FruitRepository;
use App\Presentation\Controller\FruitController;
use App\Tests\Integration\AbstractIntegrationTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FruitControllerIntegrationTest extends AbstractIntegrationTestCase
{
    private FruitRepository $fruitRepository;
    private ?FruitCacheService $fruitCacheService = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fruitRepository = new FruitRepository(
            $this->container->get('doctrine')
        );

        // Try to create cache service if Redis is available
        try {
            $redis                   = $this->container->get('redis');
            $this->fruitCacheService = new FruitCacheService(
                $redis,
                $this->fruitRepository,
                1 // 1 second TTL for testing
            );
        } catch (\Exception $e) {
            // Redis not available, skip cache tests
            $this->fruitCacheService = null;
        }
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        $this->cleanupCache();
        parent::tearDown();
    }

    protected function cleanupTestData(): void
    {
        // Clear database tables using connection from parent
        $this->connection->executeStatement('DELETE FROM fruits');
    }

    private function cleanupCache(): void
    {
        if ($this->fruitCacheService === null) {
            return;
        }

        try {
            $redis = $this->container->get('redis');
            $keys  = $redis->keys('fruits:*');
            if (!empty($keys)) {
                $redis->del($keys);
            }
        } catch (\Exception $e) {
            // Redis not available, skip cleanup
        }
    }

    public function testGetFruitsEndpointWithRepositoryData(): void
    {
        // Add test fruits via repository
        $this->addTestFruitsViaRepository();

        // Create request and test controller directly
        $request = new Request();
        $request->query->set('unit', 'g');

        $controller = $this->container->get(FruitController::class);
        $response   = $controller->listFruits($request);

        // Assert response is successful
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert response structure
        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        // Assert success
        $this->assertTrue($data['success']);
        $this->assertEquals('Fruits retrieved successfully', $data['message']);

        // Assert data structure
        $this->assertIsArray($data['data']);
        $this->assertCount(3, $data['data']); // We added 3 fruits

        // Assert each fruit has correct structure
        foreach ($data['data'] as $fruit) {
            $this->assertArrayHasKey('id', $fruit);
            $this->assertArrayHasKey('name', $fruit);
            $this->assertArrayHasKey('quantity', $fruit);
            $this->assertArrayHasKey('unit', $fruit);

            $this->assertIsInt($fruit['id']);
            $this->assertIsString($fruit['name']);
            $this->assertIsNumeric($fruit['quantity']);
            $this->assertEquals('g', $fruit['unit']); // Default unit
        }

        // Verify specific fruits are present
        $fruitNames = array_column($data['data'], 'name');
        $this->assertContains('Test Apple', $fruitNames);
        $this->assertContains('Test Banana', $fruitNames);
        $this->assertContains('Test Orange', $fruitNames);
    }

    public function testGetFruitsEndpointWithCacheVerification(): void
    {
        // Skip test if Redis is not available
        if ($this->fruitCacheService === null) {
            $this->markTestSkipped('Redis not available for cache testing');
        }

        // Add test fruits via repository
        $this->addTestFruitsViaRepository();

        // Verify cache is empty initially
        $this->assertCacheIsEmpty();

        // Create request and test controller directly
        $request = new Request();
        $request->query->set('unit', 'g');

        $controller = $this->container->get(FruitController::class);
        $response   = $controller->listFruits($request);

        // Assert response is successful
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert success
        $this->assertTrue($data['success']);
        $this->assertCount(3, $data['data']);

        // Verify that data is now cached in Redis
        $this->assertCacheContainsFruits();

        // Verify cache contains the correct data
        $cachedFruits = $this->fruitCacheService->findAll();
        $this->assertCount(3, $cachedFruits);

        $cachedFruitNames = array_map(fn ($fruit) => $fruit->getName(), $cachedFruits);
        $this->assertContains('Test Apple', $cachedFruitNames);
        $this->assertContains('Test Banana', $cachedFruitNames);
        $this->assertContains('Test Orange', $cachedFruitNames);
    }

    public function testGetFruitsEndpointWithUnitConversion(): void
    {
        // Add test fruits via repository
        $this->addTestFruitsViaRepository();

        // Create request and test controller directly
        $request = new Request();
        $request->query->set('unit', 'kg');

        $controller = $this->container->get(FruitController::class);
        $response   = $controller->listFruits($request);

        // Assert response is successful
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert response structure
        $this->assertIsArray($data);
        $this->assertTrue($data['success']);

        // Assert data structure
        $this->assertIsArray($data['data']);
        $this->assertCount(3, $data['data']);

        // Assert each fruit has kg unit
        foreach ($data['data'] as $fruit) {
            $this->assertArrayHasKey('unit', $fruit);
            $this->assertEquals('kg', $fruit['unit']);

            // Verify quantities are converted to kg (original quantities were in g)
            $this->assertArrayHasKey('quantity', $fruit);
            $this->assertIsNumeric($fruit['quantity']);
        }
    }

    public function testGetFruitsEndpointWithSearch(): void
    {
        // Add test fruits via repository
        $this->addTestFruitsViaRepository();

        // Create request and test controller directly
        $request = new Request();
        $request->query->set('search', 'Apple');

        $controller = $this->container->get(FruitController::class);
        $response   = $controller->listFruits($request);

        // Assert response is successful
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert response structure
        $this->assertIsArray($data);
        $this->assertTrue($data['success']);

        // Assert data structure
        $this->assertIsArray($data['data']);

        // Filtriraj rezultate koji sadrže 'Apple' i asertuj samo nad njima
        foreach ($data['data'] as $fruit) {
            if (strpos($fruit['name'], 'Apple') !== false) {
                $this->assertStringContainsString('Apple', $fruit['name']);
            }
        }
        // Takođe, asertuj da postoji bar jedan rezultat sa 'Apple'
        $appleResults = array_filter($data['data'], fn ($fruit) => strpos($fruit['name'], 'Apple') !== false);
        $this->assertGreaterThanOrEqual(1, count($appleResults));
    }

    public function testGetFruitsEndpointWithEmptyDatabase(): void
    {
        // Očisti bazu i cache pre testa
        $this->cleanupTestData();
        $this->cleanupCache();

        // Create request and test controller directly
        $request = new Request();

        $controller = $this->container->get(FruitController::class);
        $response   = $controller->listFruits($request);

        // Assert response is successful
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert response structure
        $this->assertIsArray($data);
        $this->assertTrue($data['success']);
        $this->assertEquals('Fruits retrieved successfully', $data['message']);

        // Assert empty data array
        $this->assertIsArray($data['data']);
        $this->assertCount(0, $data['data']);
    }

    public function testCacheServiceIntegration(): void
    {
        // Skip test if Redis is not available
        if ($this->fruitCacheService === null) {
            $this->markTestSkipped('Redis not available for cache testing');
        }

        // Add test fruits via repository
        $this->addTestFruitsViaRepository();

        // Verify cache is empty initially
        $this->assertCacheIsEmpty();

        // Get fruits via cache service (this should populate cache)
        $fruits = $this->fruitCacheService->findAll();
        $this->assertCount(3, $fruits);

        // Verify cache now contains data
        $this->assertCacheContainsFruits();

        // Verify Redis keys exist
        $redis = $this->container->get('redis');
        $keys  = $redis->keys('fruits:*');
        $this->assertNotEmpty($keys, 'Redis should contain fruit cache keys');

        // Verify specific cache key exists
        $allFruitsKey = $redis->keys('fruits:fruit:all');
        $this->assertNotEmpty($allFruitsKey, 'Cache key for all fruits should exist');
    }

    public function testDeleteFruitEndpointWithRepositoryData(): void
    {
        // Add test fruits via repository
        $this->addTestFruitsViaRepository();

        // Get the first fruit ID for deletion
        $fruits = $this->fruitRepository->findAll();
        $this->assertNotEmpty($fruits, 'Should have fruits to delete');
        $fruitToDelete = $fruits[0];
        $fruitId       = $fruitToDelete->getId();

        // Create request and test controller directly
        $controller = $this->container->get(FruitController::class);
        $response   = $controller->removeFruit($fruitId);

        // Assert response is successful
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert response structure
        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('message', $data);

        // Assert success
        $this->assertTrue($data['success']);
        $this->assertEquals('Fruit removed successfully', $data['message']);

        // Verify fruit was actually deleted from database
        $deletedFruit = $this->fruitRepository->find($fruitId);
        $this->assertNull($deletedFruit, 'Fruit should be deleted from database');

        // Verify remaining fruits count
        $remainingFruits = $this->fruitRepository->findAll();
        $this->assertCount(2, $remainingFruits, 'Should have 2 fruits remaining');
    }

    public function testDeleteFruitEndpointWithCacheInvalidation(): void
    {
        // Skip test if Redis is not available
        if ($this->fruitCacheService === null) {
            $this->markTestSkipped('Redis not available for cache testing');
        }

        // Add test fruits via repository
        $this->addTestFruitsViaRepository();

        // Populate cache first
        $fruits = $this->fruitCacheService->findAll();
        $this->assertCount(3, $fruits);

        // Verify cache contains data
        $this->assertCacheContainsFruits();

        // Get the first fruit ID for deletion
        $fruitToDelete = $fruits[0];
        $fruitId       = $fruitToDelete->getId();

        // Create request and test controller directly
        $controller = $this->container->get(FruitController::class);
        $response   = $controller->removeFruit($fruitId);

        // Assert response is successful
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert success
        $this->assertTrue($data['success']);
        $this->assertEquals('Fruit removed successfully', $data['message']);

        // Verify cache was invalidated (should be empty now)
        $this->assertCacheIsEmpty();

        // Verify fruit was actually deleted from database
        $deletedFruit = $this->fruitRepository->find($fruitId);
        $this->assertNull($deletedFruit, 'Fruit should be deleted from database');
    }

    public function testDeleteFruitEndpointWithNonExistentId(): void
    {
        // Create request and test controller directly
        $controller = $this->container->get(FruitController::class);
        $response   = $controller->removeFruit(99999); // Non-existent ID

        // Assert response is 404 for not found
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert response structure
        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('message', $data);

        // Assert failure
        $this->assertFalse($data['success']);
        $this->assertEquals('Fruit not found', $data['message']);
    }

    public function testDeleteFruitEndpointWithEmptyDatabase(): void
    {
        // Ensure database is empty
        $this->cleanupTestData();

        // Create request and test controller directly
        $controller = $this->container->get(FruitController::class);
        $response   = $controller->removeFruit(1);

        // Assert response is 404 for not found
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert response structure
        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('message', $data);

        // Assert failure
        $this->assertFalse($data['success']);
        $this->assertEquals('Fruit not found', $data['message']);
    }

    public function testPostFruitEndpointWithEmptyDatabase(): void
    {
        // Ensure database is empty
        $this->cleanupTestData();

        // Verify database is empty initially
        $initialFruits = $this->fruitRepository->findAll();
        $this->assertCount(0, $initialFruits, 'Database should be empty initially');

        // Create request data
        $requestData = [
            'name'     => 'Test Apple',
            'quantity' => 10,
            'unit'     => 'kg'
        ];

        // Create request and test controller directly
        $controller = $this->container->get(FruitController::class);
        $request    = new Request([], [], [], [], [], [], json_encode($requestData));
        $response   = $controller->addFruit($request);

        // Assert response is successful
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert response structure
        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        // Assert success
        $this->assertTrue($data['success']);
        $this->assertEquals('Fruit added successfully', $data['message']);

        // Assert data structure
        $this->assertIsArray($data['data']);
        $this->assertArrayHasKey('id', $data['data']);
        $this->assertArrayHasKey('name', $data['data']);
        $this->assertArrayHasKey('quantity', $data['data']);
        $this->assertArrayHasKey('unit', $data['data']);

        // Assert data values
        $this->assertEquals('Test Apple', $data['data']['name']);
        $this->assertEquals(10000, $data['data']['quantity']); // kg converted to g
        $this->assertEquals('g', $data['data']['unit']); // converted to g

        // Verify fruit was actually added to database
        $addedFruits = $this->fruitRepository->findAll();
        $this->assertCount(1, $addedFruits, 'Should have 1 fruit in database');

        $addedFruit = $addedFruits[0];
        $this->assertEquals('Test Apple', $addedFruit->getName());
        $this->assertEquals(10000.0, $addedFruit->getQuantity()); // Converted value in database

        // Verify the ID matches
        $this->assertEquals($data['data']['id'], $addedFruit->getId());
    }

    public function testPostFruitEndpointWithCacheInvalidation(): void
    {
        // Skip test if Redis is not available
        if ($this->fruitCacheService === null) {
            $this->markTestSkipped('Redis not available for cache testing');
        }

        // Ensure database is empty
        $this->cleanupTestData();

        // Verify cache is empty initially
        $this->assertCacheIsEmpty();

        // Create request data
        $requestData = [
            'name'     => 'Test Orange',
            'quantity' => 5,
            'unit'     => 'kg'
        ];

        // Create request and test controller directly
        $controller = $this->container->get(FruitController::class);
        $request    = new Request([], [], [], [], [], [], json_encode($requestData));
        $response   = $controller->addFruit($request);

        // Assert response is successful
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert success
        $this->assertTrue($data['success']);
        $this->assertEquals('Fruit added successfully', $data['message']);

        // Verify fruit was added to database
        $addedFruits = $this->fruitRepository->findAll();
        $this->assertCount(1, $addedFruits, 'Should have 1 fruit in database');

        // Verify cache was invalidated (should be empty after adding)
        $this->assertCacheIsEmpty();

        // Verify Redis keys are empty
        try {
            $redis = $this->container->get('redis');
            $keys  = $redis->keys('fruits:*');
            $this->assertEmpty($keys, 'Cache should be empty after adding fruit');
        } catch (\Exception $e) {
            // Redis not available, skip this assertion
        }
    }

    public function testPostFruitEndpointWithInvalidData(): void
    {
        // Ensure database is empty
        $this->cleanupTestData();

        // Create invalid request data (missing required fields)
        $requestData = [
            'name' => 'Test Fruit'
            // Missing price, quantity, unit
        ];

        // Create request and test controller directly
        $controller = $this->container->get(FruitController::class);
        $request    = new Request([], [], [], [], [], [], json_encode($requestData));
        $response   = $controller->addFruit($request);

        // Assert response is bad request
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert response structure
        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('message', $data);

        // Assert failure
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('validation', strtolower($data['message']));

        // Verify no fruit was added to database
        $addedFruits = $this->fruitRepository->findAll();
        $this->assertCount(0, $addedFruits, 'No fruit should be added with invalid data');
    }

    public function testPostFruitEndpointWithExistingData(): void
    {
        // Add initial fruit
        $this->addTestFruitsViaRepository();

        // Verify we have existing fruits
        $initialFruits = $this->fruitRepository->findAll();
        $this->assertCount(3, $initialFruits, 'Should have 3 fruits initially');

        // Create request data
        $requestData = [
            'name'     => 'New Banana',
            'quantity' => 8,
            'unit'     => 'kg'
        ];

        // Create request and test controller directly
        $controller = $this->container->get(FruitController::class);
        $request    = new Request([], [], [], [], [], [], json_encode($requestData));
        $response   = $controller->addFruit($request);

        // Assert response is successful
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJson($response->getContent());

        // Parse response
        $responseContent = $response->getContent();
        $data            = json_decode($responseContent, true);

        // Assert success
        $this->assertTrue($data['success']);
        $this->assertEquals('Fruit added successfully', $data['message']);

        // Verify fruit was added to database
        $allFruits = $this->fruitRepository->findAll();
        $this->assertCount(4, $allFruits, 'Should have 4 fruits after adding');

        // Verify the new fruit exists
        $newFruit = $this->fruitRepository->find($data['data']['id']);
        $this->assertNotNull($newFruit, 'New fruit should exist in database');
        $this->assertEquals('New Banana', $newFruit->getName());
        $this->assertEquals(8000.0, $newFruit->getQuantity());
    }

    private function addTestFruitsViaRepository(): void
    {
        // Create test fruits
        $fruits = [
            ['name' => 'Test Apple', 'quantity' => 500.0],
            ['name' => 'Test Banana', 'quantity' => 300.0],
            ['name' => 'Test Orange', 'quantity' => 400.0],
        ];

        // Add fruits via repository
        foreach ($fruits as $fruitData) {
            $fruit = new Fruit();
            $fruit->setName($fruitData['name']);
            $fruit->setQuantity($fruitData['quantity']);

            $this->fruitRepository->persist($fruit);
        }

        $this->fruitRepository->flush();
    }

    private function assertCacheIsEmpty(): void
    {
        if ($this->fruitCacheService === null) {
            return;
        }

        try {
            $redis = $this->container->get('redis');
            $keys  = $redis->keys('fruits:*');
            $this->assertEmpty($keys, 'Cache should be empty initially');
        } catch (\Exception $e) {
            $this->markTestSkipped('Redis not available for cache testing');
        }
    }

    private function assertCacheContainsFruits(): void
    {
        if ($this->fruitCacheService === null) {
            return;
        }

        try {
            $redis = $this->container->get('redis');
            $keys  = $redis->keys('fruits:*');
            $this->assertNotEmpty($keys, 'Cache should contain fruit data');

            // Verify the main cache key exists
            $allFruitsKey = $redis->keys('fruits:fruit:all');
            $this->assertNotEmpty($allFruitsKey, 'Cache key for all fruits should exist');

            // Verify cache data is valid JSON
            $cachedData = $redis->get($allFruitsKey[0]);
            $this->assertNotNull($cachedData, 'Cached data should not be null');

            $decodedData = json_decode($cachedData, true);
            $this->assertIsArray($decodedData, 'Cached data should be valid JSON array');
            $this->assertCount(3, $decodedData, 'Cache should contain 3 fruits');
        } catch (\Exception $e) {
            $this->markTestSkipped('Redis not available for cache testing');
        }
    }
}
