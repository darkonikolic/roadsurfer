<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence\Entity;

use App\Infrastructure\Persistence\Entity\Fruit;
use PHPUnit\Framework\TestCase;

class FruitTest extends TestCase
{
    public function testShouldCreateFruitWithAllProperties(): void
    {
        // Arrange
        $id = 1;
        $name = 'Apple';
        $quantity = 20000.0;
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();

        // Act
        $fruit = new Fruit();
        $fruit->setId($id);
        $fruit->setName($name);
        $fruit->setQuantity($quantity);
        $fruit->setCreatedAt($createdAt);
        $fruit->setUpdatedAt($updatedAt);

        // Assert
        $this->assertEquals($id, $fruit->getId());
        $this->assertEquals($name, $fruit->getName());
        $this->assertEquals($quantity, $fruit->getQuantity());
        $this->assertEquals($createdAt, $fruit->getCreatedAt());
        $this->assertEquals($updatedAt, $fruit->getUpdatedAt());
    }

    public function testShouldCreateFruitWithDefaultValues(): void
    {
        // Act
        $fruit = new Fruit();

        // Assert
        $this->assertNull($fruit->getId());
        $this->assertNull($fruit->getName());
        $this->assertNull($fruit->getQuantity());
        $this->assertNull($fruit->getCreatedAt());
        $this->assertNull($fruit->getUpdatedAt());
    }

    public function testShouldUpdateFruitProperties(): void
    {
        // Arrange
        $fruit = new Fruit();
        $fruit->setName('Apple');
        $fruit->setQuantity(20000.0);

        // Act
        $fruit->setName('Orange');
        $fruit->setQuantity(15000.0);

        // Assert
        $this->assertEquals('Orange', $fruit->getName());
        $this->assertEquals(15000.0, $fruit->getQuantity());
    }

    public function testShouldHandleFloatQuantity(): void
    {
        // Arrange
        $fruit = new Fruit();
        $quantity = 1234.56;

        // Act
        $fruit->setQuantity($quantity);

        // Assert
        $this->assertEquals($quantity, $fruit->getQuantity());
        $this->assertIsFloat($fruit->getQuantity());
    }

    public function testShouldHandleZeroQuantity(): void
    {
        // Arrange
        $fruit = new Fruit();

        // Act
        $fruit->setQuantity(0.0);

        // Assert
        $this->assertEquals(0.0, $fruit->getQuantity());
    }

    public function testShouldHandleLargeQuantity(): void
    {
        // Arrange
        $fruit = new Fruit();
        $largeQuantity = 999999.99;

        // Act
        $fruit->setQuantity($largeQuantity);

        // Assert
        $this->assertEquals($largeQuantity, $fruit->getQuantity());
    }
}
