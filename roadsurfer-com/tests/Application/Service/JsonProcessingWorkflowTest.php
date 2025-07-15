<?php

declare(strict_types=1);

namespace App\Tests\Application\Service;

use App\Application\Service\JsonToProductListService;
use App\Application\Service\ProductSplitterService;
use App\Application\Service\UnitConversionService;
use App\Shared\DTO\ProductListDTO;
use PHPUnit\Framework\TestCase;

class JsonProcessingWorkflowTest extends TestCase
{
    private JsonToProductListService $jsonService;
    private UnitConversionService $conversionService;
    private ProductSplitterService $splitterService;

    protected function setUp(): void
    {
        $this->jsonService = new JsonToProductListService();
        $this->conversionService = new UnitConversionService();
        $this->splitterService = new ProductSplitterService();
    }

    public function testCompleteWorkflowWithValidJson(): void
    {
        $json = '[
            {"id": 1, "name": "Apple", "type": "fruit", "quantity": 1.5, "unit": "kg"},
            {"id": 2, "name": "Carrot", "type": "vegetable", "quantity": 0.5, "unit": "kg"},
            {"id": 3, "name": "Banana", "type": "fruit", "quantity": 2.0, "unit": "kg"}
        ]';

        // Step 1: Process JSON
        $productList = $this->jsonService->process($json);
        $this->assertInstanceOf(ProductListDTO::class, $productList);
        $this->assertCount(3, $productList->getProducts());

        // Step 2: Convert units to grams
        $convertedProductList = $this->conversionService->convertProductListToGrams($productList);
        $this->assertInstanceOf(ProductListDTO::class, $convertedProductList);
        $this->assertCount(3, $convertedProductList->getProducts());

        // Step 3: Split into fruits and vegetables
        $fruits = $this->splitterService->extractFruits($convertedProductList);
        $vegetables = $this->splitterService->extractVegetables($convertedProductList);

        $this->assertCount(2, $fruits->getProducts());
        $this->assertCount(1, $vegetables->getProducts());

        // Verify quantities are in grams
        foreach ($fruits->getProducts() as $fruit) {
            $this->assertEquals('g', $fruit->unit);
            $this->assertGreaterThan(0, $fruit->quantity);
        }

        foreach ($vegetables->getProducts() as $vegetable) {
            $this->assertEquals('g', $vegetable->unit);
            $this->assertGreaterThan(0, $vegetable->quantity);
        }
    }

    public function testCompleteWorkflowWithMixedUnits(): void
    {
        $json = '[
            {"id": 1, "name": "Apple", "type": "fruit", "quantity": 1.5, "unit": "kg"},
            {"id": 2, "name": "Carrot", "type": "vegetable", "quantity": 500, "unit": "g"},
            {"id": 3, "name": "Banana", "type": "fruit", "quantity": 750, "unit": "g"}
        ]';

        // Step 1: Process JSON
        $productList = $this->jsonService->process($json);
        $this->assertInstanceOf(ProductListDTO::class, $productList);
        $this->assertCount(3, $productList->getProducts());

        // Step 2: Convert units to grams
        $convertedProductList = $this->conversionService->convertProductListToGrams($productList);
        $this->assertInstanceOf(ProductListDTO::class, $convertedProductList);
        $this->assertCount(3, $convertedProductList->getProducts());

        // Step 3: Split into fruits and vegetables
        $fruits = $this->splitterService->extractFruits($convertedProductList);
        $vegetables = $this->splitterService->extractVegetables($convertedProductList);

        $this->assertCount(2, $fruits->getProducts());
        $this->assertCount(1, $vegetables->getProducts());

        // Verify all quantities are in grams
        foreach ($convertedProductList->getProducts() as $product) {
            $this->assertEquals('g', $product->unit);
        }
    }

    public function testCompleteWorkflowWithEmptyJson(): void
    {
        $json = '[]';

        // Step 1: Process JSON
        $productList = $this->jsonService->process($json);
        $this->assertInstanceOf(ProductListDTO::class, $productList);
        $this->assertCount(0, $productList->getProducts());

        // Step 2: Convert units to grams
        $convertedProductList = $this->conversionService->convertProductListToGrams($productList);
        $this->assertInstanceOf(ProductListDTO::class, $convertedProductList);
        $this->assertCount(0, $convertedProductList->getProducts());

        // Step 3: Split into fruits and vegetables
        $fruits = $this->splitterService->extractFruits($convertedProductList);
        $vegetables = $this->splitterService->extractVegetables($convertedProductList);

        $this->assertCount(0, $fruits->getProducts());
        $this->assertCount(0, $vegetables->getProducts());
    }
}
