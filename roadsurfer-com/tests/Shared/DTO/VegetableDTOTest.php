<?php

declare(strict_types=1);

namespace App\Tests\Shared\DTO;

use App\Shared\DTO\ProductDTO;
use App\Shared\DTO\VegetableDTO;
use PHPUnit\Framework\TestCase;

class VegetableDTOTest extends TestCase
{
    public function testShouldCreateVegetableDTOWithCorrectType(): void
    {
        // Arrange
        $id = 1;
        $name = 'Carrot';
        $quantity = 1.5;
        $unit = 'kg';

        // Act
        $vegetableDTO = new VegetableDTO($id, $name, $quantity, $unit);

        // Assert
        $this->assertEquals($id, $vegetableDTO->productId);
        $this->assertEquals($name, $vegetableDTO->name);
        $this->assertEquals('vegetable', $vegetableDTO->type);
        $this->assertEquals($quantity, $vegetableDTO->quantity);
        $this->assertEquals($unit, $vegetableDTO->unit);
    }

    public function testShouldCreateVegetableDTOUsingStaticCreateMethod(): void
    {
        // Arrange
        $id = 1;
        $name = 'Carrot';
        $quantity = 1.5;
        $unit = 'kg';

        // Act
        $vegetableDTO = VegetableDTO::create($id, $name, 'vegetable', $quantity, $unit);

        // Assert
        $this->assertEquals($id, $vegetableDTO->productId);
        $this->assertEquals($name, $vegetableDTO->name);
        $this->assertEquals('vegetable', $vegetableDTO->type);
        $this->assertEquals($quantity, $vegetableDTO->quantity);
        $this->assertEquals($unit, $vegetableDTO->unit);
    }

    public function testShouldCreateVegetableDTOFromProductDTO(): void
    {
        // Arrange
        $productDTO = new ProductDTO(1, 'Carrot', 'vegetable', 1.5, 'kg');

        // Act
        $vegetableDTO = VegetableDTO::fromProductDTO($productDTO);

        // Assert
        $this->assertEquals($productDTO->productId, $vegetableDTO->productId);
        $this->assertEquals($productDTO->name, $vegetableDTO->name);
        $this->assertEquals('vegetable', $vegetableDTO->type);
        $this->assertEquals($productDTO->quantity, $vegetableDTO->quantity);
        $this->assertEquals($productDTO->unit, $vegetableDTO->unit);
    }

    public function testShouldThrowExceptionWhenCreatingVegetableFromNonVegetableProduct(): void
    {
        // Arrange
        $productDTO = new ProductDTO(1, 'Apple', 'fruit', 2.5, 'kg');

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product must be of type vegetable');
        VegetableDTO::fromProductDTO($productDTO);
    }

    public function testShouldBeImmutable(): void
    {
        // Arrange
        $vegetableDTO = new VegetableDTO(1, 'Carrot', 1.5, 'kg');

        // Act & Assert
        $this->expectException(\Error::class);
        $vegetableDTO->name = 'Potato';
    }
}
