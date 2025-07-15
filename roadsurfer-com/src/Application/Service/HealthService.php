<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\External\Health\DatabaseHealthService;
use App\Infrastructure\External\Health\RedisHealthService;
use App\Shared\DTO\HealthStatusDTO;
use DateTimeImmutable;

class HealthService
{
    public function __construct(
        private readonly DatabaseHealthService $databaseHealth,
        private readonly RedisHealthService $redisHealth,
        private readonly string $environment = 'dev',
    ) {
    }

    public function getSystemHealth(): HealthStatusDTO
    {
        return new HealthStatusDTO(
            database: $this->databaseHealth->check(),
            redis: $this->redisHealth->check(),
            timestamp: new DateTimeImmutable(),
            environment: $this->environment
        );
    }
}
