<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Cache;

use App\Infrastructure\Persistence\Entity\Fruit;
use Redis;

class FruitCacheService
{
    private const CACHE_TTL_ALL = 3600; // 1 hour
    private const CACHE_TTL_SEARCH = 1800; // 30 minutes
    private const CACHE_PREFIX = 'fruits:';

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

    /**
     * @return array|null
     */
    public function getFruits(): ?array
    {
        $cacheKey = self::CACHE_PREFIX . 'all';
        $cachedData = $this->redis->get($cacheKey);

        if ($cachedData === false) {
            return null;
        }

        return json_decode($cachedData, true);
    }

    /**
     * @param Fruit[] $fruits
     */
    public function setFruits(array $fruits): void
    {
        $cacheKey = self::CACHE_PREFIX . 'all';
        $data = $this->serializeFruits($fruits);

        $this->redis->setex($cacheKey, self::CACHE_TTL_ALL, $data);
    }

    /**
     * @return array|null
     */
    public function getFruitsByName(string $searchTerm): ?array
    {
        $cacheKey = self::CACHE_PREFIX . 'search:' . md5($searchTerm);
        $cachedData = $this->redis->get($cacheKey);

        if ($cachedData === false) {
            return null;
        }

        return json_decode($cachedData, true);
    }

    /**
     * @param Fruit[] $fruits
     */
    public function setFruitsByName(string $searchTerm, array $fruits): void
    {
        $cacheKey = self::CACHE_PREFIX . 'search:' . md5($searchTerm);
        $data = $this->serializeFruits($fruits);

        $this->redis->setex($cacheKey, self::CACHE_TTL_SEARCH, $data);
    }

    public function invalidateCache(): void
    {
        $pattern = self::CACHE_PREFIX . '*';
        $keys = $this->redis->keys($pattern);

        if (! empty($keys)) {
            foreach ($keys as $key) {
                $this->redis->del($key);
            }
        }
    }

    // Methods for management services
    public function getCachedFruits(?string $search = null): ?array
    {
        if ($search) {
            return $this->getFruitsByName($search);
        }

        return $this->getFruits();
    }

    public function cacheFruits(array $fruits, ?string $search = null): void
    {
        if ($search) {
            $this->setFruitsByName($search, $fruits);

            return;
        }

        $this->setFruits($fruits);
    }

    /**
     * @param Fruit[] $fruits
     */
    private function serializeFruits(array $fruits): string
    {
        $data = [];
        foreach ($fruits as $fruit) {
            $data[] = [
                'id' => $fruit->getId(),
                'name' => $fruit->getName(),
                'quantity' => $fruit->getQuantity(),
                'created_at' => $fruit->getCreatedAt()?->format('c'),
                'updated_at' => $fruit->getUpdatedAt()?->format('c'),
            ];
        }

        return json_encode($data);
    }
}
