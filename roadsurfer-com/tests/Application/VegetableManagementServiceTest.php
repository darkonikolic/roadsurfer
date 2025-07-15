<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Service\UnitConversionService;
use App\Application\Service\VegetableManagementService;
use App\Infrastructure\External\Cache\VegetableCacheService;
use App\Infrastructure\Persistence\Entity\Vegetable;
use App\Infrastructure\Persistence\Entity\VegetableRepository;
use App\Shared\DTO\ApiResponseDTO;
use App\Shared\DTO\VegetableApiRequestDTO;
use PHPUnit\Framework\TestCase;

class VegetableManagementServiceTest extends TestCase
{
    private VegetableManagementService $service;
    private VegetableRepository $repository;
    private VegetableCacheService $cacheService;
    private UnitConversionService $unitConversionService;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(VegetableRepository::class);
        $this->cacheService = $this->createMock(VegetableCacheService::class);
        $this->unitConversionService = $this->createMock(UnitConversionService::class);

        $this->service = new VegetableManagementService(
            $this->repository,
            $this->cacheService,
            $this->unitConversionService
        );
    }

    public function testAddVegetableSuccess(): void
    {
        $request = new VegetableApiRequestDTO('Carrot', 0.5, 'kg');
        $vegetable = new Vegetable();
        $vegetable->setName('Carrot');
        $vegetable->setQuantity(500);

        $this->unitConversionService
            ->expects($this->once())
            ->method('convertToGrams')
            ->with(0.5, 'kg')
            ->willReturn(500.0);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Vegetable::class), true);

        $this->cacheService
            ->expects($this->once())
            ->method('invalidateCache');

        $response = $this->service->addVegetable($request);

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertTrue($response->success);
        $this->assertEquals('Vegetable added successfully', $response->message);
        $this->assertIsArray($response->data);
        $this->assertArrayHasKey('id', $response->data);
        $this->assertArrayHasKey('name', $response->data);
        $this->assertArrayHasKey('quantity', $response->data);
        $this->assertArrayHasKey('unit', $response->data);
    }

    public function testAddVegetableFailure(): void
    {
        $request = new VegetableApiRequestDTO('Carrot', 0.5, 'kg');

        $this->unitConversionService
            ->expects($this->once())
            ->method('convertToGrams')
            ->willThrowException(new \Exception('Database error'));

        $response = $this->service->addVegetable($request);

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertFalse($response->success);
        $this->assertStringContainsString('Failed to add vegetable', $response->message);
    }

    public function testRemoveVegetableSuccess(): void
    {
        $vegetable = new Vegetable();
        $vegetable->setId(1);
        $vegetable->setName('Carrot');
        $vegetable->setQuantity(500);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($vegetable);

        $this->repository
            ->expects($this->once())
            ->method('remove')
            ->with($vegetable, true);

        $this->cacheService
            ->expects($this->once())
            ->method('invalidateCache');

        $response = $this->service->removeVegetable(1);

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertTrue($response->success);
        $this->assertEquals('Vegetable removed successfully', $response->message);
    }

    public function testRemoveVegetableNotFound(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $response = $this->service->removeVegetable(999);

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals('Vegetable not found', $response->message);
    }

    public function testListVegetablesSuccess(): void
    {
        $vegetables = [
            (new Vegetable())->setId(1)->setName('Carrot')->setQuantity(500),
            (new Vegetable())->setId(2)->setName('Broccoli')->setQuantity(300),
        ];

        $this->cacheService
            ->expects($this->once())
            ->method('getCachedVegetables')
            ->with('carrot')
            ->willReturn(null);

        $this->repository
            ->expects($this->once())
            ->method('findBySearch')
            ->with('carrot')
            ->willReturn($vegetables);

        $this->cacheService
            ->expects($this->once())
            ->method('cacheVegetables')
            ->with($vegetables, 'carrot');

        $this->unitConversionService
            ->expects($this->exactly(2))
            ->method('convertToKilograms')
            ->willReturn(0.5, 0.3);

        $response = $this->service->listVegetables('carrot', 'kg');

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertTrue($response->success);
        $this->assertEquals('Vegetables retrieved successfully', $response->message);
        $this->assertIsArray($response->data);
        $this->assertCount(2, $response->data);
    }

    public function testListVegetablesFromCache(): void
    {
        $vegetables = [
            (new Vegetable())->setId(1)->setName('Carrot')->setQuantity(500),
        ];

        $this->cacheService
            ->expects($this->once())
            ->method('getCachedVegetables')
            ->with(null)
            ->willReturn($vegetables);

        $this->unitConversionService
            ->expects($this->once())
            ->method('convertToKilograms')
            ->willReturn(0.5);

        $response = $this->service->listVegetables(null, 'kg');

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertTrue($response->success);
        $this->assertEquals('Vegetables retrieved successfully', $response->message);
        $this->assertIsArray($response->data);
        $this->assertCount(1, $response->data);
    }

    public function testListVegetablesFailure(): void
    {
        $this->cacheService
            ->expects($this->once())
            ->method('getCachedVegetables')
            ->willThrowException(new \Exception('Cache error'));

        $response = $this->service->listVegetables();

        $this->assertInstanceOf(ApiResponseDTO::class, $response);
        $this->assertFalse($response->success);
        $this->assertStringContainsString('Failed to retrieve vegetables', $response->message);
    }
}
