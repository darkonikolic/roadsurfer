<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence\Entity;

use App\Infrastructure\Persistence\Entity\Vegetable;
use PHPUnit\Framework\TestCase;

class VegetableTest extends TestCase
{
    public function testShouldCreateVegetableWithAllProperties(): void
    {
        // Arrange
        $id = 1;
        $name = 'Carrot';
        $quantity = 10922.0;
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();

        // Act
        $vegetable = new Vegetable();
        $vegetable->setId($id);
        $vegetable->setName($name);
        $vegetable->setQuantity($quantity);
        $vegetable->setCreatedAt($createdAt);
        $vegetable->setUpdatedAt($updatedAt);

        // Assert
        $this->assertEquals($id, $vegetable->getId());
        $this->assertEquals($name, $vegetable->getName());
        $this->assertEquals($quantity, $vegetable->getQuantity());
        $this->assertEquals($createdAt, $vegetable->getCreatedAt());
        $this->assertEquals($updatedAt, $vegetable->getUpdatedAt());
    }

    public function testShouldCreateVegetableWithDefaultValues(): void
    {
        // Act
        $vegetable = new Vegetable();

        // Assert
        $this->assertNull($vegetable->getId());
        $this->assertNull($vegetable->getName());
        $this->assertNull($vegetable->getQuantity());
        $this->assertNull($vegetable->getCreatedAt());
        $this->assertNull($vegetable->getUpdatedAt());
    }

    public function testShouldUpdateVegetableProperties(): void
    {
        // Arrange
        $vegetable = new Vegetable();
        $vegetable->setName('Carrot');
        $vegetable->setQuantity(10922.0);

        // Act
        $vegetable->setName('Potato');
        $vegetable->setQuantity(5000.0);

        // Assert
        $this->assertEquals('Potato', $vegetable->getName());
        $this->assertEquals(5000.0, $vegetable->getQuantity());
    }

    public function testShouldHandleFloatQuantity(): void
    {
        // Arrange
        $vegetable = new Vegetable();
        $quantity = 5678.90;

        // Act
        $vegetable->setQuantity($quantity);

        // Assert
        $this->assertEquals($quantity, $vegetable->getQuantity());
        $this->assertIsFloat($vegetable->getQuantity());
    }

    public function testShouldHandleZeroQuantity(): void
    {
        // Arrange
        $vegetable = new Vegetable();

        // Act
        $vegetable->setQuantity(0.0);

        // Assert
        $this->assertEquals(0.0, $vegetable->getQuantity());
    }

    public function testShouldHandleLargeQuantity(): void
    {
        // Arrange
        $vegetable = new Vegetable();
        $largeQuantity = 999999.99;

        // Act
        $vegetable->setQuantity($largeQuantity);

        // Assert
        $this->assertEquals($largeQuantity, $vegetable->getQuantity());
    }
}
