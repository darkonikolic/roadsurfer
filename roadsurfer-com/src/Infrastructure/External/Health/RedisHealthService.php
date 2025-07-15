<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Health;

use App\Shared\DTO\ServiceHealthDTO;
use Redis;

class RedisHealthService
{
    private Redis $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
        $this->connectRedis();
    }

    private function connectRedis(): void
    {
        if (! $this->redis->isConnected()) {
            $host = getenv('REDIS_HOST') ?: 'redis';
            $port = (int) (getenv('REDIS_PORT') ?: 6379);
            $database = (int) (getenv('REDIS_DB') ?: 0);

            $this->redis->connect($host, $port);
            $this->redis->select($database);
        }
    }

    public function check(): ServiceHealthDTO
    {
        try {
            $this->redis->ping();

            return new ServiceHealthDTO(
                status: 'ok',
                connected: true
            );
        } catch (\Exception $e) {
            return new ServiceHealthDTO(
                status: 'error',
                connected: false,
                error: $e->getMessage()
            );
        }
    }
}
