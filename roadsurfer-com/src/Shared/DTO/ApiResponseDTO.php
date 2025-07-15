<?php

declare(strict_types=1);

namespace App\Shared\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class ApiResponseDTO
{
    /**
     * @param array<int, string> $errors
     */
    public function __construct(
        #[Groups(['api'])]
        public bool $success,
        #[Groups(['api'])]
        public string $message,
        #[Groups(['api'])]
        public mixed $data = null,
        #[Groups(['api'])]
        public array $errors = [],
    ) {
    }

    public static function createSuccess(string $message, mixed $data = null): self
    {
        return new self(true, $message, $data);
    }

    /**
     * @param array<int, string> $errors
     */
    public static function error(array $errors): self
    {
        return new self(false, 'Error occurred', null, $errors);
    }
}
