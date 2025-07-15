<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Cache;

use App\Infrastructure\Persistence\Entity\Vegetable;
use App\Shared\DTO\VegetableDTO;

class VegetableCacheService
{
    private const CACHE_TTL_ALL    = 3600; // 1 hour
    private const CACHE_TTL_SEARCH = 1800; // 30 minutes
    private const CACHE_PREFIX     = 'vegetables:';

    private \Redis $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
        $this->connectRedis();
    }

    private function connectRedis(): void
    {
        if (!$this->redis->isConnected()) {
            $host     = getenv('REDIS_HOST') ?: 'redis';
            $port     = (int)(getenv('REDIS_PORT') ?: 6379);
            $database = (int)(getenv('REDIS_DB') ?: 0);

            $this->redis->connect($host, $port);
            $this->redis->select($database);
        }
    }

    /**
     * @return array<VegetableDTO>
     */
    public function getVegetables(): array
    {
        $cacheKey   = self::CACHE_PREFIX . 'all';
        $cachedData = $this->redis->get($cacheKey);

        if (false === $cachedData) {
            return [];
        }

        return unserialize($cachedData);
    }

    /**
     * @param Vegetable[] $vegetables
     */
    public function setVegetables(array $vegetables): void
    {
        $cacheKey = self::CACHE_PREFIX . 'all';
        $data     = $this->serializeVegetables($vegetables);

        $this->redis->setex($cacheKey, self::CACHE_TTL_ALL, $data);
    }

    /**
     * @return array<VegetableDTO>
     */
    public function getVegetablesByName(string $searchTerm): array
    {
        $cacheKey   = self::CACHE_PREFIX . 'search:' . md5($searchTerm);
        $cachedData = $this->redis->get($cacheKey);

        if (false === $cachedData) {
            return [];
        }

        return unserialize($cachedData);
    }

    /**
     * @param Vegetable[] $vegetables
     */
    public function setVegetablesByName(string $searchTerm, array $vegetables): void
    {
        $cacheKey = self::CACHE_PREFIX . 'search:' . md5($searchTerm);
        $data     = $this->serializeVegetables($vegetables);

        $this->redis->setex($cacheKey, self::CACHE_TTL_SEARCH, $data);
    }

    public function invalidateCache(): void
    {
        $pattern = self::CACHE_PREFIX . '*';
        $keys    = $this->redis->keys($pattern);

        if (!empty($keys)) {
            foreach ($keys as $key) {
                $this->redis->del($key);
            }
        }
    }

    // Methods for management services
    /**
     * @return array<VegetableDTO>
     */
    public function getCachedVegetables(): array
    {
        return $this->getVegetables();
    }

    /**
     * @param array<VegetableDTO> $vegetables
     */
    public function cacheVegetables(array $vegetables): void
    {
        $cacheKey = self::CACHE_PREFIX . 'all';
        $this->redis->setex($cacheKey, self::CACHE_TTL_ALL, serialize($vegetables));
    }

    /**
     * @param Vegetable[] $vegetables
     */
    private function serializeVegetables(array $vegetables): string
    {
        $data = [];
        foreach ($vegetables as $vegetable) {
            $data[] = [
                'id'         => $vegetable->getId(),
                'name'       => $vegetable->getName(),
                'quantity'   => $vegetable->getQuantity(),
                'created_at' => $vegetable->getCreatedAt()?->format('c'),
                'updated_at' => $vegetable->getUpdatedAt()?->format('c'),
            ];
        }

        return json_encode($data);
    }
}
