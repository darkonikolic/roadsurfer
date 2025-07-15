<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Cache;

use App\Infrastructure\Persistence\Entity\Fruit;
use App\Infrastructure\Persistence\Repository\FruitRepository;
use App\Shared\DTO\FruitDTO;
use DateTime;
use Redis;

class FruitCacheService extends AbstractCacheService
{
    private FruitRepository $fruitRepository;

    public function __construct(Redis $redis, FruitRepository $fruitRepository, int $cacheTtl = 1)
    {
        parent::__construct($redis, $cacheTtl);
        $this->fruitRepository = $fruitRepository;
    }

    /**
     * Get all fruits (with cache fallback to repository)
     * 
     * @return array<Fruit>
     */
    public function findAll(): array
    {
        $cached = $this->read(['fruit', 'all']);
        
        if (empty($cached)) {
            $fruits = $this->fruitRepository->findAll();
            $this->save(['fruit', 'all'], $this->convertToDTOs($fruits));
            return $fruits;
        }
        
        return $this->hydrateFromCache($cached);
    }

    /**
     * Find fruits by name (with cache fallback to repository)
     * 
     * @param string $name
     *
     * @return array<Fruit>
     */
    public function findByName(string $name): array
    {
        $cached = $this->read(['fruit', 'name', $name]);
        
        if (empty($cached)) {
            $fruits = $this->fruitRepository->findByName($name);
            $this->save(['fruit', 'name', $name], $this->convertToDTOs($fruits));
            return $fruits;
        }
        
        return $this->hydrateFromCache($cached);
    }

    /**
     * Convert Fruit entities to FruitDTOs
     * 
     * @param array<Fruit> $fruits
     *
     * @return array<FruitDTO>
     */
    private function convertToDTOs(array $fruits): array
    {
        $dtos = [];
        foreach ($fruits as $fruit) {
            $dto = new FruitDTO(
                $fruit->getId(),
                $fruit->getName(),
                $fruit->getQuantity(),
                'kg' // Default unit
            );
            $dtos[] = $dto;
        }
        return $dtos;
    }

    /**
     * Hydrate cached data back to Fruit entities
     * 
     * @param array<array<string, mixed>> $cachedData
     *
     * @return array<Fruit>
     */
    private function hydrateFromCache(array $cachedData): array
    {
        $fruits = [];
        foreach ($cachedData as $data) {
            $fruit = new Fruit();
            $fruit->setId($data['id']);
            $fruit->setName($data['name']);
            $fruit->setQuantity($data['quantity']);
            if (isset($data['created_at'])) {
                $fruit->setCreatedAt(new DateTime($data['created_at']));
            }
            if (isset($data['updated_at'])) {
                $fruit->setUpdatedAt(new DateTime($data['updated_at']));
            }
            $fruits[] = $fruit;
        }
        return $fruits;
    }

    /**
     * Get cache prefix
     */
    protected function getCachePrefix(): string
    {
        return 'fruits:';
    }

    /**
     * Check if item is valid DTO
     * 
     * @param mixed $item
     */
    protected function isValidDTO($item): bool
    {
        return $item instanceof FruitDTO;
    }

    /**
     * Convert DTO to array
     * 
     * @param mixed $item
     *
     * @return array<string, mixed>
     */
    protected function convertDTOToArray($item): array
    {
        return [
            'id'         => $item->productId,
            'name'       => $item->name,
            'quantity'   => $item->quantity,
            'created_at' => null, // DTO doesn't have timestamps
            'updated_at' => null,
        ];
    }
}
