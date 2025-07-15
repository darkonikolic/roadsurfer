<?php

declare(strict_types=1);

namespace App\Shared\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{
    public function __construct(
        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public readonly ?int $productId,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $name,
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['fruit', 'vegetable'])]
        public readonly string $type,
        #[Assert\NotNull]
        #[Assert\Type('numeric')]
        #[Assert\Positive]
        public readonly float $quantity,
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['kg', 'g'])]
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
