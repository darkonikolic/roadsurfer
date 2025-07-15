<?php

declare(strict_types=1);

namespace App\Tests\Shared\DTO;

use App\Shared\DTO\ProductDTO;
use PHPUnit\Framework\TestCase;

class ProductDTOTest extends TestCase
{
    public function testShouldCreateProductDTOWithAllProperties(): void
    {
        // Arrange
        $id = 1;
        $name = 'Apple';
        $type = 'fruit';
        $quantity = 2.5;
        $unit = 'kg';

        // Act
        $productDTO = new ProductDTO($id, $name, $type, $quantity, $unit);

        // Assert
        $this->assertEquals($id, $productDTO->productId);
        $this->assertEquals($name, $productDTO->name);
        $this->assertEquals($type, $productDTO->type);
        $this->assertEquals($quantity, $productDTO->quantity);
        $this->assertEquals($unit, $productDTO->unit);
    }

    public function testShouldCreateProductDTOWithNullId(): void
    {
        // Arrange
        $name = 'Apple';
        $type = 'fruit';
        $quantity = 2.5;
        $unit = 'kg';

        // Act
        $productDTO = new ProductDTO(null, $name, $type, $quantity, $unit);

        // Assert
        $this->assertNull($productDTO->productId);
        $this->assertEquals($name, $productDTO->name);
        $this->assertEquals($type, $productDTO->type);
        $this->assertEquals($quantity, $productDTO->quantity);
        $this->assertEquals($unit, $productDTO->unit);
    }

    public function testShouldCreateProductDTOUsingStaticCreateMethod(): void
    {
        // Arrange
        $id = 1;
        $name = 'Apple';
        $type = 'fruit';
        $quantity = 2.5;
        $unit = 'kg';

        // Act
        $productDTO = ProductDTO::create($id, $name, $type, $quantity, $unit);

        // Assert
        $this->assertEquals($id, $productDTO->productId);
        $this->assertEquals($name, $productDTO->name);
        $this->assertEquals($type, $productDTO->type);
        $this->assertEquals($quantity, $productDTO->quantity);
        $this->assertEquals($unit, $productDTO->unit);
    }

    public function testShouldBeImmutable(): void
    {
        // Arrange
        $productDTO = new ProductDTO(1, 'Apple', 'fruit', 2.5, 'kg');

        // Act & Assert
        $this->expectException(\Error::class);
        $productDTO->name = 'Orange';
    }
}
