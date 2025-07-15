<?php

declare(strict_types=1);

namespace App\Shared\DTO;

class ServiceHealthDTO
{
    public function __construct(
        public string $status,
        public bool $connected,
        public ?string $error = null,
    ) {
    }

    public function isHealthy(): bool
    {
        return 'ok' === $this->status && $this->connected;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'status'    => $this->status,
            'connected' => $this->connected,
        ];

        if (null !== $this->error) {
            $data['error'] = $this->error;
        }

        return $data;
    }
}
