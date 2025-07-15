<?php

declare(strict_types=1);

namespace App\Shared\DTO;

use InvalidArgumentException;

class FruitDTO extends ProductDTO
{
    public function __construct(
        ?int $productId,
        string $name,
        float $quantity,
        string $unit,
    ) {
        parent::__construct($productId, $name, 'fruit', $quantity, $unit);
    }

    public static function create(
        ?int $productId,
        string $name,
        string $type,
        float $quantity,
        string $unit,
    ): self {
        if ('fruit' !== $type) {
            throw new InvalidArgumentException('Type must be fruit');
        }

        return new self($productId, $name, $quantity, $unit);
    }

    public static function fromProductDTO(ProductDTO $productDTO): self
    {
        if ('fruit' !== $productDTO->type) {
            throw new InvalidArgumentException('Product must be of type fruit');
        }

        return new self($productDTO->productId, $productDTO->name, $productDTO->quantity, $productDTO->unit);
    }
}
