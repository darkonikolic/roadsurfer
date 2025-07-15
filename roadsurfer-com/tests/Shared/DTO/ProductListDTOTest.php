<?php

declare(strict_types=1);

namespace App\Tests\Shared\DTO;

use App\Shared\DTO\ProductDTO;
use App\Shared\DTO\ProductListDTO;
use PHPUnit\Framework\TestCase;

class ProductListDTOTest extends TestCase
{
    public function testShouldCreateEmptyProductList(): void
    {
        // Act
        $productListDTO = new ProductListDTO();

        // Assert
        $this->assertEmpty($productListDTO->products);
        $this->assertEquals(0, $productListDTO->count());
        $this->assertTrue($productListDTO->isEmpty());
    }

    public function testShouldCreateProductListWithProducts(): void
    {
        // Arrange
        $products = [
            new ProductDTO(1, 'Apple', 'fruit', 2.5, 'kg'),
            new ProductDTO(2, 'Banana', 'fruit', 1.0, 'kg'),
        ];

        // Act
        $productListDTO = new ProductListDTO($products);

        // Assert
        $this->assertCount(2, $productListDTO->products);
        $this->assertEquals(2, $productListDTO->count());
        $this->assertFalse($productListDTO->isEmpty());
    }

    public function testShouldCreateProductListUsingStaticCreateMethod(): void
    {
        // Arrange
        $products = [
            new ProductDTO(1, 'Apple', 'fruit', 2.5, 'kg'),
        ];

        // Act
        $productListDTO = ProductListDTO::create($products);

        // Assert
        $this->assertCount(1, $productListDTO->products);
        $this->assertEquals(1, $productListDTO->count());
    }

    public function testShouldAddProductToList(): void
    {
        // Arrange
        $productListDTO = new ProductListDTO();
        $product = new ProductDTO(1, 'Apple', 'fruit', 2.5, 'kg');

        // Act
        $newProductListDTO = $productListDTO->addProduct($product);

        // Assert
        $this->assertCount(1, $newProductListDTO->products);
        $this->assertEquals(1, $newProductListDTO->count());
        $this->assertFalse($newProductListDTO->isEmpty());
    }

    public function testShouldGetProductsArray(): void
    {
        // Arrange
        $products = [
            new ProductDTO(1, 'Apple', 'fruit', 2.5, 'kg'),
            new ProductDTO(2, 'Banana', 'fruit', 1.0, 'kg'),
        ];
        $productListDTO = new ProductListDTO($products);

        // Act
        $result = $productListDTO->getProducts();

        // Assert
        $this->assertSame($products, $result);
        $this->assertCount(2, $result);
    }

    public function testShouldBeImmutable(): void
    {
        // Arrange
        $productListDTO = new ProductListDTO();

        // Act & Assert
        $this->expectException(\Error::class);
        $productListDTO->products = [new ProductDTO(1, 'Apple', 'fruit', 2.5, 'kg')];
    }
}
