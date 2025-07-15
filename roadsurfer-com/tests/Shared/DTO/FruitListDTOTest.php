<?php

declare(strict_types=1);

namespace App\Tests\Shared\DTO;

use App\Shared\DTO\FruitDTO;
use App\Shared\DTO\FruitListDTO;
use PHPUnit\Framework\TestCase;

class FruitListDTOTest extends TestCase
{
    public function testShouldCreateEmptyFruitList(): void
    {
        // Act
        $fruitListDTO = new FruitListDTO();

        // Assert
        $this->assertEmpty($fruitListDTO->fruits);
        $this->assertEquals(0, $fruitListDTO->count());
        $this->assertTrue($fruitListDTO->isEmpty());
    }

    public function testShouldCreateFruitListWithFruits(): void
    {
        // Arrange
        $fruits = [
            new FruitDTO(1, 'Apple', 2.5, 'kg'),
            new FruitDTO(2, 'Banana', 1.0, 'kg'),
        ];

        // Act
        $fruitListDTO = new FruitListDTO($fruits);

        // Assert
        $this->assertCount(2, $fruitListDTO->fruits);
        $this->assertEquals(2, $fruitListDTO->count());
        $this->assertFalse($fruitListDTO->isEmpty());
    }

    public function testShouldCreateFruitListUsingStaticCreateMethod(): void
    {
        // Arrange
        $fruits = [
            new FruitDTO(1, 'Apple', 2.5, 'kg'),
        ];

        // Act
        $fruitListDTO = FruitListDTO::create($fruits);

        // Assert
        $this->assertCount(1, $fruitListDTO->fruits);
        $this->assertEquals(1, $fruitListDTO->count());
    }

    public function testShouldAddFruitToList(): void
    {
        // Arrange
        $fruitListDTO = new FruitListDTO();
        $fruit = new FruitDTO(1, 'Apple', 2.5, 'kg');

        // Act
        $newFruitListDTO = $fruitListDTO->addFruit($fruit);

        // Assert
        $this->assertCount(1, $newFruitListDTO->fruits);
        $this->assertEquals(1, $newFruitListDTO->count());
        $this->assertFalse($newFruitListDTO->isEmpty());
    }

    public function testShouldGetFruitsArray(): void
    {
        // Arrange
        $fruits = [
            new FruitDTO(1, 'Apple', 2.5, 'kg'),
            new FruitDTO(2, 'Banana', 1.0, 'kg'),
        ];
        $fruitListDTO = new FruitListDTO($fruits);

        // Act
        $result = $fruitListDTO->getFruits();

        // Assert
        $this->assertSame($fruits, $result);
        $this->assertCount(2, $result);
    }

    public function testShouldBeImmutable(): void
    {
        // Arrange
        $fruitListDTO = new FruitListDTO();

        // Act & Assert
        $this->expectException(\Error::class);
        $fruitListDTO->fruits = [new FruitDTO(1, 'Apple', 2.5, 'kg')];
    }
}
