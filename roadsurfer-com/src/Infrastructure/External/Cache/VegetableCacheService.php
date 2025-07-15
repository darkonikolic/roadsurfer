<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Cache;

use App\Infrastructure\Persistence\Entity\Vegetable;
use App\Infrastructure\Persistence\Repository\VegetableRepository;
use App\Shared\DTO\VegetableDTO;
use DateTime;
use Redis;

class VegetableCacheService extends AbstractCacheService
{
    private VegetableRepository $vegetableRepository;

    public function __construct(Redis $redis, VegetableRepository $vegetableRepository, int $cacheTtl = 1)
    {
        parent::__construct($redis, $cacheTtl);
        $this->vegetableRepository = $vegetableRepository;
    }

    /**
     * Get all vegetables (with cache fallback to repository)
     * 
     * @return array<Vegetable>
     */
    public function findAll(): array
    {
        $cached = $this->read(['vegetable', 'all']);
        
        if (empty($cached)) {
            $vegetables = $this->vegetableRepository->findAll();
            $this->save(['vegetable', 'all'], $this->convertToDTOs($vegetables));
            return $vegetables;
        }
        
        return $this->hydrateFromCache($cached);
    }

    /**
     * Find vegetables by name (with cache fallback to repository)
     * 
     * @param string $name
     *
     * @return array<Vegetable>
     */
    public function findByName(string $name): array
    {
        $cached = $this->read(['vegetable', 'name', $name]);
        
        if (empty($cached)) {
            $vegetables = $this->vegetableRepository->findByName($name);
            $this->save(['vegetable', 'name', $name], $this->convertToDTOs($vegetables));
            return $vegetables;
        }
        
        return $this->hydrateFromCache($cached);
    }

    /**
     * Convert Vegetable entities to VegetableDTOs
     * 
     * @param array<Vegetable> $vegetables
     *
     * @return array<VegetableDTO>
     */
    private function convertToDTOs(array $vegetables): array
    {
        $dtos = [];
        foreach ($vegetables as $vegetable) {
            $dto = new VegetableDTO(
                $vegetable->getId(),
                $vegetable->getName(),
                $vegetable->getQuantity(),
                'kg' // Default unit
            );
            $dtos[] = $dto;
        }
        return $dtos;
    }

    /**
     * Hydrate cached data back to Vegetable entities
     * 
     * @param array<array<string, mixed>> $cachedData
     *
     * @return array<Vegetable>
     */
    private function hydrateFromCache(array $cachedData): array
    {
        $vegetables = [];
        foreach ($cachedData as $data) {
            $vegetable = new Vegetable();
            $vegetable->setId($data['id']);
            $vegetable->setName($data['name']);
            $vegetable->setQuantity($data['quantity']);
            if (isset($data['created_at'])) {
                $vegetable->setCreatedAt(new DateTime($data['created_at']));
            }
            if (isset($data['updated_at'])) {
                $vegetable->setUpdatedAt(new DateTime($data['updated_at']));
            }
            $vegetables[] = $vegetable;
        }
        return $vegetables;
    }

    /**
     * Get cache prefix
     */
    protected function getCachePrefix(): string
    {
        return 'vegetables:';
    }

    /**
     * Check if item is valid DTO
     * 
     * @param mixed $item
     */
    protected function isValidDTO($item): bool
    {
        return $item instanceof VegetableDTO;
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
