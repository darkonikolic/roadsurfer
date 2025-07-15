<?php

declare(strict_types=1);

namespace App\Shared\DTO;

readonly class ServiceHealthDTO
{
    public function __construct(
        public string $status,
        public bool $connected,
        public ?string $error = null
    ) {
    }

    public function isHealthy(): bool
    {
        return $this->status === 'ok' && $this->connected;
    }

    public function toArray(): array
    {
        $data = [
            'status' => $this->status,
            'connected' => $this->connected,
        ];

        if ($this->error !== null) {
            $data['error'] = $this->error;
        }

        return $data;
    }
}
