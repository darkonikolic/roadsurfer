<?php

declare(strict_types=1);

namespace App\Shared\DTO;

readonly class HealthStatusDTO
{
    public function __construct(
        public ServiceHealthDTO $database,
        public ServiceHealthDTO $redis,
        public \DateTimeImmutable $timestamp,
        public string $environment
    ) {
    }

    public function isHealthy(): bool
    {
        return $this->database->isHealthy() && $this->redis->isHealthy();
    }

    public function toArray(): array
    {
        return [
            'status' => $this->isHealthy() ? 'healthy' : 'unhealthy',
            'timestamp' => $this->timestamp->format('c'),
            'environment' => $this->environment,
            'services' => [
                'database' => $this->database->toArray(),
                'redis' => $this->redis->toArray(),
                'application' => 'ok',
            ],
        ];
    }
}
