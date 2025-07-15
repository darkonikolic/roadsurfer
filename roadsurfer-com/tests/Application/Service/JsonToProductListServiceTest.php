<?php

declare(strict_types=1);

namespace App\Tests\Application\Service;

use App\Application\Service\JsonToProductListService;
use App\Shared\DTO\ProductDTO;
use App\Shared\DTO\ProductListDTO;
use PHPUnit\Framework\TestCase;

class JsonToProductListServiceTest extends TestCase
{
    private JsonToProductListService $service;

    protected function setUp(): void
    {
        $this->service = new JsonToProductListService();
    }

    public function testShouldProcessValidJsonAndReturnProductListDTO(): void
    {
        // Arrange
        $json = '[
            {
                "id": 1,
                "name": "Carrot",
                "type": "vegetable",
                "quantity": 10922,
                "unit": "g"
            },
            {
                "id": 2,
                "name": "Apples",
                "type": "fruit",
                "quantity": 20,
                "unit": "kg"
            }
        ]';

        // Act
        $result = $this->service->process($json);

        // Assert
        $this->assertInstanceOf(ProductListDTO::class, $result);
        $this->assertCount(2, $result->getProducts());

        $firstProduct = $result->getProducts()[0];
        $this->assertInstanceOf(ProductDTO::class, $firstProduct);
        $this->assertEquals(1, $firstProduct->productId);
        $this->assertEquals('Carrot', $firstProduct->name);
        $this->assertEquals('vegetable', $firstProduct->type);
        $this->assertEquals(10922.0, $firstProduct->quantity);
        $this->assertEquals('g', $firstProduct->unit);
    }

    public function testShouldProcessEmptyJsonAndReturnEmptyProductList(): void
    {
        // Arrange
        $json = '[]';

        // Act
        $result = $this->service->process($json);

        // Assert
        $this->assertInstanceOf(ProductListDTO::class, $result);
        $this->assertCount(0, $result->getProducts());
        $this->assertTrue($result->isEmpty());
    }

    public function testShouldThrowExceptionForInvalidJson(): void
    {
        // Arrange
        $invalidJson = '{invalid json}';

        // Act & Assert
        $this->expectException(\JsonException::class);
        $this->service->process($invalidJson);
    }

    public function testShouldThrowExceptionForMissingRequiredFields(): void
    {
        // Arrange
        $json = '[
            {
                "id": 1,
                "name": "Carrot",
                "quantity": 10922,
                "unit": "g"
            }
        ]';

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required field: type');
        $this->service->process($json);
    }

    public function testShouldThrowExceptionForInvalidQuantityType(): void
    {
        // Arrange
        $json = '[
            {
                "id": 1,
                "name": "Carrot",
                "type": "vegetable",
                "quantity": "invalid",
                "unit": "g"
            }
        ]';

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be a number');
        $this->service->process($json);
    }

    public function testShouldThrowExceptionForInvalidUnit(): void
    {
        // Arrange
        $json = '[
            {
                "id": 1,
                "name": "Carrot",
                "type": "vegetable",
                "quantity": 10922,
                "unit": "invalid"
            }
        ]';

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unit must be kg or g');
        $this->service->process($json);
    }

    public function testShouldThrowExceptionForInvalidType(): void
    {
        // Arrange
        $json = '[
            {
                "id": 1,
                "name": "Carrot",
                "type": "invalid",
                "quantity": 10922,
                "unit": "g"
            }
        ]';

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type must be fruit or vegetable');
        $this->service->process($json);
    }
}
