<?php

declare(strict_types=1);

namespace App\Tests\Application\Service;

use App\Application\Service\UnitConversionService;
use App\Shared\DTO\ProductDTO;
use App\Shared\DTO\ProductListDTO;
use PHPUnit\Framework\TestCase;

class UnitConversionServiceTest extends TestCase
{
    private UnitConversionService $service;

    protected function setUp(): void
    {
        $this->service = new UnitConversionService();
    }

    public function testConvertToGramsWithKilograms(): void
    {
        $products = [
            new ProductDTO(1, 'Apple', 'fruit', 1.5, 'kg'),
            new ProductDTO(2, 'Banana', 'fruit', 2.0, 'kg'),
        ];

        $productList = new ProductListDTO($products);
        $result = $this->service->convertProductListToGrams($productList);

        $this->assertInstanceOf(ProductListDTO::class, $result);
        $this->assertCount(2, $result->getProducts());

        $firstProduct = $result->getProducts()[0];
        $this->assertEquals(1500, $firstProduct->quantity);
        $this->assertEquals('g', $firstProduct->unit);

        $secondProduct = $result->getProducts()[1];
        $this->assertEquals(2000, $secondProduct->quantity);
        $this->assertEquals('g', $secondProduct->unit);
    }

    public function testConvertToGramsWithGrams(): void
    {
        $products = [
            new ProductDTO(1, 'Apple', 'fruit', 500, 'g'),
            new ProductDTO(2, 'Banana', 'fruit', 750, 'g'),
        ];

        $productList = new ProductListDTO($products);
        $result = $this->service->convertProductListToGrams($productList);

        $this->assertInstanceOf(ProductListDTO::class, $result);
        $this->assertCount(2, $result->getProducts());

        $firstProduct = $result->getProducts()[0];
        $this->assertEquals(500, $firstProduct->quantity);
        $this->assertEquals('g', $firstProduct->unit);

        $secondProduct = $result->getProducts()[1];
        $this->assertEquals(750, $secondProduct->quantity);
        $this->assertEquals('g', $secondProduct->unit);
    }

    public function testConvertToGramsWithMixedUnits(): void
    {
        $products = [
            new ProductDTO(1, 'Apple', 'fruit', 1.5, 'kg'),
            new ProductDTO(2, 'Banana', 'fruit', 750, 'g'),
        ];

        $productList = new ProductListDTO($products);
        $result = $this->service->convertProductListToGrams($productList);

        $this->assertInstanceOf(ProductListDTO::class, $result);
        $this->assertCount(2, $result->getProducts());

        $firstProduct = $result->getProducts()[0];
        $this->assertEquals(1500, $firstProduct->quantity);
        $this->assertEquals('g', $firstProduct->unit);

        $secondProduct = $result->getProducts()[1];
        $this->assertEquals(750, $secondProduct->quantity);
        $this->assertEquals('g', $secondProduct->unit);
    }

    public function testConvertToGramsWithEmptyList(): void
    {
        $productList = new ProductListDTO([]);
        $result = $this->service->convertProductListToGrams($productList);

        $this->assertInstanceOf(ProductListDTO::class, $result);
        $this->assertCount(0, $result->getProducts());
    }

    public function testConvertToGramsPreservesOtherProperties(): void
    {
        $products = [
            new ProductDTO(1, 'Apple', 'fruit', 1.5, 'kg'),
        ];

        $productList = new ProductListDTO($products);
        $result = $this->service->convertProductListToGrams($productList);

        $convertedProduct = $result->getProducts()[0];
        $this->assertEquals(1, $convertedProduct->productId);
        $this->assertEquals('Apple', $convertedProduct->name);
        $this->assertEquals('fruit', $convertedProduct->type);
        $this->assertEquals(1500, $convertedProduct->quantity);
        $this->assertEquals('g', $convertedProduct->unit);
    }

    public function testConvertToGramsMethod(): void
    {
        $result = $this->service->convertToGrams(1.5, 'kg');
        $this->assertEquals(1500, $result);

        $result = $this->service->convertToGrams(500, 'g');
        $this->assertEquals(500, $result);
    }

    public function testConvertToKilogramsMethod(): void
    {
        $result = $this->service->convertToKilograms(1500);
        $this->assertEquals(1.5, $result);

        $result = $this->service->convertToKilograms(500);
        $this->assertEquals(0.5, $result);
    }
}
