<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Service\FruitManagementService;
use App\Application\Service\UnitConversionService;
use App\Infrastructure\External\Cache\FruitCacheService;
use App\Infrastructure\Persistence\Entity\Fruit;
use App\Infrastructure\Persistence\Entity\FruitRepository;
use App\Shared\DTO\ApiResponseDTO;
use App\Shared\DTO\FruitApiRequestDTO;
use PHPUnit\Framework\TestCase;

class FruitManagementServiceTest extends TestCase
{
    private FruitManagementService $service;
    private FruitRepository $repository;
    private FruitCacheService $cacheService;
    private UnitConversionService $unitConversionService;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(FruitRepository::class);
        $this->cacheService = $this->createMock(FruitCacheService::class);
        $this->unitConversionService = $this->createMock(UnitConversionService::class);

        $this->service = new FruitManagementService(
            $this->repository,
            $this->cacheService,
            $this->unitConversionService
        );
    }

    public function testAddFruitSuccess(): void
    {
        $request = new FruitApiRequestDTO('Apple', 1.5, 'kg');
        $fruit = new Fruit();
        $fruit->setName('Apple');
        $fruit->setQuantity(1500);

        $this->unitConversionService
            ->expects($this->once())
            ->method('convertToGrams')
            ->with(1.5, 'kg')
            ->willReturn(1500.0);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Fruit::class), true);

        $this->cacheService
            ->expects($this->once())
            ->method('invalidateCache');

        $response = $this->service->addFruit($request);

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertTrue($response->success);
        $this->assertEquals('Fruit added successfully', $response->message);
        $this->assertIsArray($response->data);
        $this->assertArrayHasKey('id', $response->data);
        $this->assertArrayHasKey('name', $response->data);
        $this->assertArrayHasKey('quantity', $response->data);
        $this->assertArrayHasKey('unit', $response->data);
    }

    public function testAddFruitFailure(): void
    {
        $request = new FruitApiRequestDTO('Apple', 1.5, 'kg');

        $this->unitConversionService
            ->expects($this->once())
            ->method('convertToGrams')
            ->willThrowException(new \Exception('Database error'));

        $response = $this->service->addFruit($request);

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertFalse($response->success);
        $this->assertStringContainsString('Failed to add fruit', $response->message);
    }

    public function testRemoveFruitSuccess(): void
    {
        $fruit = new Fruit();
        $fruit->setId(1);
        $fruit->setName('Apple');
        $fruit->setQuantity(1500);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($fruit);

        $this->repository
            ->expects($this->once())
            ->method('remove')
            ->with($fruit, true);

        $this->cacheService
            ->expects($this->once())
            ->method('invalidateCache');

        $response = $this->service->removeFruit(1);

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertTrue($response->success);
        $this->assertEquals('Fruit removed successfully', $response->message);
    }

    public function testRemoveFruitNotFound(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $response = $this->service->removeFruit(999);

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals('Fruit not found', $response->message);
    }

    public function testListFruitsSuccess(): void
    {
        $fruits = [
            (new Fruit())->setId(1)->setName('Apple')->setQuantity(1500),
            (new Fruit())->setId(2)->setName('Banana')->setQuantity(2000),
        ];

        $this->cacheService
            ->expects($this->once())
            ->method('getCachedFruits')
            ->with('apple')
            ->willReturn(null);

        $this->repository
            ->expects($this->once())
            ->method('findBySearch')
            ->with('apple')
            ->willReturn($fruits);

        $this->cacheService
            ->expects($this->once())
            ->method('cacheFruits')
            ->with($fruits, 'apple');

        $this->unitConversionService
            ->expects($this->exactly(2))
            ->method('convertToKilograms')
            ->willReturn(1.5, 2.0);

        $response = $this->service->listFruits('apple', 'kg');

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertTrue($response->success);
        $this->assertEquals('Fruits retrieved successfully', $response->message);
        $this->assertIsArray($response->data);
        $this->assertCount(2, $response->data);
    }

    public function testListFruitsFromCache(): void
    {
        $fruits = [
            (new Fruit())->setId(1)->setName('Apple')->setQuantity(1500),
        ];

        $this->cacheService
            ->expects($this->once())
            ->method('getCachedFruits')
            ->with(null)
            ->willReturn($fruits);

        $this->unitConversionService
            ->expects($this->once())
            ->method('convertToKilograms')
            ->willReturn(1.5);

        $response = $this->service->listFruits(null, 'kg');

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertTrue($response->success);
        $this->assertEquals('Fruits retrieved successfully', $response->message);
        $this->assertIsArray($response->data);
        $this->assertCount(1, $response->data);
    }

    public function testListFruitsFailure(): void
    {
        $this->cacheService
            ->expects($this->once())
            ->method('getCachedFruits')
            ->willThrowException(new \Exception('Cache error'));

        $response = $this->service->listFruits();

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertFalse($response->success);
        $this->assertStringContainsString('Failed to retrieve fruits', $response->message);
    }
}
