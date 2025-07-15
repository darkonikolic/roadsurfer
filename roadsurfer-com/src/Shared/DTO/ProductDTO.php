<?php

declare(strict_types=1);

namespace App\Shared\DTO;

class ProductDTO
{
    public function __construct(
        public readonly ?int $productId,
        public readonly string $name,
        public readonly string $type,
        public readonly float $quantity,
        public readonly string $unit,
    ) {
    }

    public static function create(
        ?int $productId,
        string $name,
        string $type,
        float $quantity,
        string $unit,
    ): self {
        return new self($productId, $name, $type, $quantity, $unit);
    }
}
