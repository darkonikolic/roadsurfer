<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Cache;

use App\Infrastructure\Persistence\Entity\Fruit;
use App\Shared\DTO\FruitDTO;

class FruitCacheService
{
    private const CACHE_TTL_ALL    = 3600; // 1 hour
    private const CACHE_TTL_SEARCH = 1800; // 30 minutes
    private const CACHE_PREFIX     = 'fruits:';

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
     * @return array<FruitDTO>
     */
    public function getFruits(): array
    {
        $cacheKey   = self::CACHE_PREFIX . 'all';
        $cachedData = $this->redis->get($cacheKey);

        if (false === $cachedData) {
            return [];
        }

        return unserialize($cachedData);
    }

    /**
     * @param Fruit[] $fruits
     */
    public function setFruits(array $fruits): void
    {
        $cacheKey = self::CACHE_PREFIX . 'all';
        $data     = $this->serializeFruits($fruits);

        $this->redis->setex($cacheKey, self::CACHE_TTL_ALL, $data);
    }

    /**
     * @return array<FruitDTO>
     */
    public function getFruitsByName(string $name): array
    {
        $cacheKey   = self::CACHE_PREFIX . 'search:' . md5($name);
        $cachedData = $this->redis->get($cacheKey);

        if (false === $cachedData) {
            return [];
        }

        return json_decode($cachedData, true);
    }

    /**
     * @param Fruit[] $fruits
     */
    public function setFruitsByName(string $searchTerm, array $fruits): void
    {
        $cacheKey = self::CACHE_PREFIX . 'search:' . md5($searchTerm);
        $data     = $this->serializeFruits($fruits);

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
     * @return array<FruitDTO>
     */
    public function getCachedFruits(): array
    {
        return $this->getFruits();
    }

    /**
     * @param array<FruitDTO> $fruits
     */
    public function cacheFruits(array $fruits): void
    {
        $cacheKey = self::CACHE_PREFIX . 'all';
        $this->redis->setex($cacheKey, self::CACHE_TTL_ALL, serialize($fruits));
    }

    /**
     * @param Fruit[] $fruits
     */
    private function serializeFruits(array $fruits): string
    {
        $data = [];
        foreach ($fruits as $fruit) {
            $data[] = [
                'id'         => $fruit->getId(),
                'name'       => $fruit->getName(),
                'quantity'   => $fruit->getQuantity(),
                'created_at' => $fruit->getCreatedAt()?->format('c'),
                'updated_at' => $fruit->getUpdatedAt()?->format('c'),
            ];
        }

        return json_encode($data);
    }
}
