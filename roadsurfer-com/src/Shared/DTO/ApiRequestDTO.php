<?php

declare(strict_types=1);

namespace App\Shared\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ApiRequestDTO
{
    #[Assert\NotBlank(message: 'Request data cannot be blank')]
    public string $data;

    public function __construct(string $data = '')
    {
        $this->data = $data;
    }
}
