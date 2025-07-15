<?php

declare(strict_types=1);

namespace App\Tests\Shared\DTO;

use App\Shared\DTO\VegetableDTO;
use App\Shared\DTO\VegetableListDTO;
use PHPUnit\Framework\TestCase;

class VegetableListDTOTest extends TestCase
{
    public function testShouldCreateEmptyVegetableList(): void
    {
        // Act
        $vegetableListDTO = new VegetableListDTO();

        // Assert
        $this->assertEmpty($vegetableListDTO->vegetables);
        $this->assertEquals(0, $vegetableListDTO->count());
        $this->assertTrue($vegetableListDTO->isEmpty());
    }

    public function testShouldCreateVegetableListWithVegetables(): void
    {
        // Arrange
        $vegetables = [
            new VegetableDTO(1, 'Carrot', 1.5, 'kg'),
            new VegetableDTO(2, 'Potato', 2.0, 'kg'),
        ];

        // Act
        $vegetableListDTO = new VegetableListDTO($vegetables);

        // Assert
        $this->assertCount(2, $vegetableListDTO->vegetables);
        $this->assertEquals(2, $vegetableListDTO->count());
        $this->assertFalse($vegetableListDTO->isEmpty());
    }

    public function testShouldCreateVegetableListUsingStaticCreateMethod(): void
    {
        // Arrange
        $vegetables = [
            new VegetableDTO(1, 'Carrot', 1.5, 'kg'),
        ];

        // Act
        $vegetableListDTO = VegetableListDTO::create($vegetables);

        // Assert
        $this->assertCount(1, $vegetableListDTO->vegetables);
        $this->assertEquals(1, $vegetableListDTO->count());
    }

    public function testShouldAddVegetableToList(): void
    {
        // Arrange
        $vegetableListDTO = new VegetableListDTO();
        $vegetable = new VegetableDTO(1, 'Carrot', 1.5, 'kg');

        // Act
        $newVegetableListDTO = $vegetableListDTO->addVegetable($vegetable);

        // Assert
        $this->assertCount(1, $newVegetableListDTO->vegetables);
        $this->assertEquals(1, $newVegetableListDTO->count());
        $this->assertFalse($newVegetableListDTO->isEmpty());
    }

    public function testShouldGetVegetablesArray(): void
    {
        // Arrange
        $vegetables = [
            new VegetableDTO(1, 'Carrot', 1.5, 'kg'),
            new VegetableDTO(2, 'Potato', 2.0, 'kg'),
        ];
        $vegetableListDTO = new VegetableListDTO($vegetables);

        // Act
        $result = $vegetableListDTO->getVegetables();

        // Assert
        $this->assertSame($vegetables, $result);
        $this->assertCount(2, $result);
    }

    public function testShouldBeImmutable(): void
    {
        // Arrange
        $vegetableListDTO = new VegetableListDTO();

        // Act & Assert
        $this->expectException(\Error::class);
        $vegetableListDTO->vegetables = [new VegetableDTO(1, 'Carrot', 1.5, 'kg')];
    }
}
