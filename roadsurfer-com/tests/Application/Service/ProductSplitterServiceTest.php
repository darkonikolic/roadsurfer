<?php

declare(strict_types=1);

namespace App\Tests\Application\Service;

use App\Application\Service\ProductSplitterService;
use App\Shared\DTO\FruitListDTO;
use App\Shared\DTO\ProductDTO;
use App\Shared\DTO\ProductListDTO;
use App\Shared\DTO\VegetableListDTO;
use PHPUnit\Framework\TestCase;

class ProductSplitterServiceTest extends TestCase
{
    private ProductSplitterService $service;

    protected function setUp(): void
    {
        $this->service = new ProductSplitterService();
    }

    public function testShouldSplitProductsIntoFruitsAndVegetables(): void
    {
        // Arrange
        $products = [
            new ProductDTO(1, 'Apples', 'fruit', 20000.0, 'g'),
            new ProductDTO(2, 'Carrot', 'vegetable', 10922.0, 'g'),
            new ProductDTO(3, 'Pears', 'fruit', 3500.0, 'g'),
            new ProductDTO(4, 'Beans', 'vegetable', 65000.0, 'g'),
        ];
        $productList = new ProductListDTO($products);

        // Act
        $result = $this->service->split($productList);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('fruits', $result);
        $this->assertArrayHasKey('vegetables', $result);

        $fruits = $result['fruits'];
        $this->assertInstanceOf(FruitListDTO::class, $fruits);
        $this->assertCount(2, $fruits->getFruits());

        $vegetables = $result['vegetables'];
        $this->assertInstanceOf(VegetableListDTO::class, $vegetables);
        $this->assertCount(2, $vegetables->getVegetables());
    }

    public function testShouldHandleOnlyFruits(): void
    {
        // Arrange
        $products = [
            new ProductDTO(1, 'Apples', 'fruit', 20000.0, 'g'),
            new ProductDTO(2, 'Pears', 'fruit', 3500.0, 'g'),
        ];
        $productList = new ProductListDTO($products);

        // Act
        $result = $this->service->split($productList);

        // Assert
        $fruits = $result['fruits'];
        $this->assertInstanceOf(FruitListDTO::class, $fruits);
        $this->assertCount(2, $fruits->getFruits());

        $vegetables = $result['vegetables'];
        $this->assertInstanceOf(VegetableListDTO::class, $vegetables);
        $this->assertCount(0, $vegetables->getVegetables());
        $this->assertTrue($vegetables->isEmpty());
    }

    public function testShouldHandleOnlyVegetables(): void
    {
        // Arrange
        $products = [
            new ProductDTO(1, 'Carrot', 'vegetable', 10922.0, 'g'),
            new ProductDTO(2, 'Beans', 'vegetable', 65000.0, 'g'),
        ];
        $productList = new ProductListDTO($products);

        // Act
        $result = $this->service->split($productList);

        // Assert
        $fruits = $result['fruits'];
        $this->assertInstanceOf(FruitListDTO::class, $fruits);
        $this->assertCount(0, $fruits->getFruits());
        $this->assertTrue($fruits->isEmpty());

        $vegetables = $result['vegetables'];
        $this->assertInstanceOf(VegetableListDTO::class, $vegetables);
        $this->assertCount(2, $vegetables->getVegetables());
    }

    public function testShouldHandleEmptyProductList(): void
    {
        // Arrange
        $productList = new ProductListDTO();

        // Act
        $result = $this->service->split($productList);

        // Assert
        $fruits = $result['fruits'];
        $this->assertInstanceOf(FruitListDTO::class, $fruits);
        $this->assertCount(0, $fruits->getFruits());
        $this->assertTrue($fruits->isEmpty());

        $vegetables = $result['vegetables'];
        $this->assertInstanceOf(VegetableListDTO::class, $vegetables);
        $this->assertCount(0, $vegetables->getVegetables());
        $this->assertTrue($vegetables->isEmpty());
    }

    public function testShouldPreserveProductProperties(): void
    {
        // Arrange
        $products = [
            new ProductDTO(1, 'Apples', 'fruit', 20000.0, 'g'),
            new ProductDTO(2, 'Carrot', 'vegetable', 10922.0, 'g'),
        ];
        $productList = new ProductListDTO($products);

        // Act
        $result = $this->service->split($productList);

        // Assert
        $fruits = $result['fruits']->getFruits();
        $this->assertCount(1, $fruits);
        $fruit = $fruits[0];
        $this->assertEquals(1, $fruit->productId);
        $this->assertEquals('Apples', $fruit->name);
        $this->assertEquals('fruit', $fruit->type);
        $this->assertEquals(20000.0, $fruit->quantity);
        $this->assertEquals('g', $fruit->unit);

        $vegetables = $result['vegetables']->getVegetables();
        $this->assertCount(1, $vegetables);
        $vegetable = $vegetables[0];
        $this->assertEquals(2, $vegetable->productId);
        $this->assertEquals('Carrot', $vegetable->name);
        $this->assertEquals('vegetable', $vegetable->type);
        $this->assertEquals(10922.0, $vegetable->quantity);
        $this->assertEquals('g', $vegetable->unit);
    }
}
