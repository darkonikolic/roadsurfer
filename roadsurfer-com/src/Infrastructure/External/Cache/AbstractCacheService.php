<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Cache;

use DateTime;
use InvalidArgumentException;
use Redis;

abstract class AbstractCacheService
{
    protected Redis $redis;
    protected int $cacheTtl;

    public function __construct(Redis $redis, int $cacheTtl = 1)
    {
        $this->redis    = $redis;
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * Save data to cache
     * 
     * @param array<mixed> $nameParams
     * @param array<mixed> $list
     *
     * @throws InvalidArgumentException
     */
    protected function save(array $nameParams = [], array $list = []): void
    {
        $cacheKey = $this->getCachePrefix() . implode(':', $nameParams);
        
        $data = [];
        foreach ($list as $item) {
            if (!$this->isValidDTO($item)) {
                throw new InvalidArgumentException('All items must be valid DTO instances');
            }
            $data[] = $this->convertDTOToArray($item);
        }
        
        $this->redis->setex($cacheKey, $this->cacheTtl, json_encode($data));
    }

    /**
     * Read data from cache
     * 
     * @param array<mixed> $nameParams
     *
     * @return array<array<string, mixed>>
     */
    protected function read(array $nameParams = []): array
    {
        $cacheKey   = $this->getCachePrefix() . implode(':', $nameParams);
        $cachedData = $this->redis->get($cacheKey);

        if (false === $cachedData) {
            return [];
        }

        return json_decode($cachedData, true);
    }

    /**
     * Invalidate cache
     */
    public function invalidateCache(): void
    {
        $pattern = $this->getCachePrefix() . '*';
        $keys    = $this->redis->keys($pattern);

        if (!empty($keys)) {
            foreach ($keys as $key) {
                $this->redis->del($key);
            }
        }
    }

    /**
     * Get cache key for given parameters
     * 
     * @param array<mixed> $nameParams
     */
    public function getCacheKey(array $nameParams = []): string
    {
        return $this->getCachePrefix() . implode(':', $nameParams);
    }

    /**
     * Get cache prefix
     */
    abstract protected function getCachePrefix(): string;

    /**
     * Check if item is valid DTO
     * 
     * @param mixed $item
     */
    abstract protected function isValidDTO($item): bool;

    /**
     * Convert DTO to array
     * 
     * @param mixed $item
     *
     * @return array<string, mixed>
     */
    abstract protected function convertDTOToArray($item): array;
}
