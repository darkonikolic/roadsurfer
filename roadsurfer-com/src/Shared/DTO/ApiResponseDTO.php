<?php

declare(strict_types=1);

namespace App\Shared\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class ApiResponseDTO
{
    public function __construct(
        #[Groups(['api'])]
        public bool $success,
        #[Groups(['api'])]
        public string $message,
        #[Groups(['api'])]
        public mixed $data = null,
        #[Groups(['api'])]
        public array $errors = []
    ) {
    }

    public static function createSuccess(string $message, mixed $data = null): self
    {
        return new self(true, $message, $data);
    }

    public static function error(string $message, array $errors = []): self
    {
        return new self(false, $message, null, $errors);
    }
}
