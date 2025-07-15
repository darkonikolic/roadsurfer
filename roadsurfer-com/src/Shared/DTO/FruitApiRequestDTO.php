<?php

declare(strict_types=1);

namespace App\Shared\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FruitApiRequestDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Fruit name cannot be blank')]
        #[Assert\Length(min: 1, max: 255, minMessage: 'Fruit name must be at least 1 character', maxMessage: 'Fruit name cannot exceed 255 characters')]
        public string $name = '',
        #[Assert\NotNull(message: 'Quantity cannot be null')]
        #[Assert\Positive(message: 'Quantity must be positive')]
        public float $quantity = 0.0,
        #[Assert\Choice(choices: ['kg', 'g'], message: 'Unit must be either kg or g')]
        public string $unit = 'kg',
    ) {
    }
}
