<?php

declare(strict_types=1);

namespace App\Shared\DTO;

readonly class FruitListDTO
{
    /**
     * @param FruitDTO[] $fruits
     */
    public function __construct(
        public array $fruits = []
    ) {
    }

    public static function create(array $fruits = []): self
    {
        return new self($fruits);
    }

    public function addFruit(FruitDTO $fruit): self
    {
        $fruits = $this->fruits;
        $fruits[] = $fruit;

        return new self($fruits);
    }

    public function getFruits(): array
    {
        return $this->fruits;
    }

    public function count(): int
    {
        return count($this->fruits);
    }

    public function isEmpty(): bool
    {
        return empty($this->fruits);
    }
}
