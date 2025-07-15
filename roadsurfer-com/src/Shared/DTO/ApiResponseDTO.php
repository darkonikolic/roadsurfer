<?php

declare(strict_types=1);

namespace App\Shared\DTO;

class ApiResponseDTO
{
    /**
     * @param array<int, string> $errors
     */
    public function __construct(
        public bool $success,
        public string $message,
        public mixed $data = null,
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
