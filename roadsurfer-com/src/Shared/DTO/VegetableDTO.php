<?php

declare(strict_types=1);

namespace App\Shared\DTO;

use App\Shared\DTO\ProductDTO;
use InvalidArgumentException;

class VegetableDTO extends ProductDTO
{
    public function __construct(
        ?int $productId,
        string $name,
        float $quantity,
        string $unit,
    ) {
        parent::__construct($productId, $name, 'vegetable', $quantity, $unit);
    }

    public static function create(
        ?int $productId,
        string $name,
        string $type,
        float $quantity,
        string $unit,
    ): self {
        if ('vegetable' !== $type) {
            throw new InvalidArgumentException('Type must be vegetable');
        }

        return new self($productId, $name, $quantity, $unit);
    }

    public static function fromProductDTO(ProductDTO $productDTO): self
    {
        if ('vegetable' !== $productDTO->type) {
            throw new InvalidArgumentException('Product must be of type vegetable');
        }

        return new self($productDTO->productId, $productDTO->name, $productDTO->quantity, $productDTO->unit);
    }
}
