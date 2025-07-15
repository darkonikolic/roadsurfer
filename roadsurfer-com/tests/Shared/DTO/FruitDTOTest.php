<?php

declare(strict_types=1);

namespace App\Tests\Shared\DTO;

use App\Shared\DTO\FruitDTO;
use App\Shared\DTO\ProductDTO;
use PHPUnit\Framework\TestCase;

class FruitDTOTest extends TestCase
{
    public function testShouldCreateFruitDTOWithCorrectType(): void
    {
        // Arrange
        $id = 1;
        $name = 'Apple';
        $quantity = 2.5;
        $unit = 'kg';

        // Act
        $fruitDTO = new FruitDTO($id, $name, $quantity, $unit);

        // Assert
        $this->assertEquals($id, $fruitDTO->productId);
        $this->assertEquals($name, $fruitDTO->name);
        $this->assertEquals('fruit', $fruitDTO->type);
        $this->assertEquals($quantity, $fruitDTO->quantity);
        $this->assertEquals($unit, $fruitDTO->unit);
    }

    public function testShouldCreateFruitDTOUsingStaticCreateMethod(): void
    {
        // Arrange
        $id = 1;
        $name = 'Apple';
        $quantity = 2.5;
        $unit = 'kg';

        // Act
        $fruitDTO = FruitDTO::create($id, $name, 'fruit', $quantity, $unit);

        // Assert
        $this->assertEquals($id, $fruitDTO->productId);
        $this->assertEquals($name, $fruitDTO->name);
        $this->assertEquals('fruit', $fruitDTO->type);
        $this->assertEquals($quantity, $fruitDTO->quantity);
        $this->assertEquals($unit, $fruitDTO->unit);
    }

    public function testShouldCreateFruitDTOFromProductDTO(): void
    {
        // Arrange
        $productDTO = new ProductDTO(1, 'Apple', 'fruit', 2.5, 'kg');

        // Act
        $fruitDTO = FruitDTO::fromProductDTO($productDTO);

        // Assert
        $this->assertEquals($productDTO->productId, $fruitDTO->productId);
        $this->assertEquals($productDTO->name, $fruitDTO->name);
        $this->assertEquals('fruit', $fruitDTO->type);
        $this->assertEquals($productDTO->quantity, $fruitDTO->quantity);
        $this->assertEquals($productDTO->unit, $fruitDTO->unit);
    }

    public function testShouldThrowExceptionWhenCreatingFruitFromNonFruitProduct(): void
    {
        // Arrange
        $productDTO = new ProductDTO(1, 'Carrot', 'vegetable', 1.0, 'kg');

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product must be of type fruit');
        FruitDTO::fromProductDTO($productDTO);
    }

    public function testShouldBeImmutable(): void
    {
        // Arrange
        $fruitDTO = new FruitDTO(1, 'Apple', 2.5, 'kg');

        // Act & Assert
        $this->expectException(\Error::class);
        $fruitDTO->name = 'Orange';
    }
}
