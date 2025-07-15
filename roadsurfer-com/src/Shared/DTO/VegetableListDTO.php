<?php

declare(strict_types=1);

namespace App\Shared\DTO;

readonly class VegetableListDTO
{
    /**
     * @param VegetableDTO[] $vegetables
     */
    public function __construct(
        public array $vegetables = []
    ) {
    }

    public static function create(array $vegetables = []): self
    {
        return new self($vegetables);
    }

    public function addVegetable(VegetableDTO $vegetable): self
    {
        $vegetables = $this->vegetables;
        $vegetables[] = $vegetable;

        return new self($vegetables);
    }

    public function getVegetables(): array
    {
        return $this->vegetables;
    }

    public function count(): int
    {
        return count($this->vegetables);
    }

    public function isEmpty(): bool
    {
        return empty($this->vegetables);
    }
}
