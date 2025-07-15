<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\External\Cache\FruitCacheService;
use App\Infrastructure\Persistence\Entity\Fruit;
use App\Infrastructure\Persistence\Repository\FruitRepository;
use App\Shared\DTO\ApiResponseDTO;
use App\Shared\DTO\FruitApiRequestDTO;
use App\Shared\DTO\FruitDTO;

class FruitManagementService
{
    public function __construct(
        private readonly FruitRepository $fruitRepository,
        private readonly FruitCacheService $fruitCacheService,
        private readonly UnitConversionService $conversionService,
    ) {
    }

    public function addFruit(FruitApiRequestDTO $request): ApiResponseDTO
    {
        try {
            $quantityInGrams = $this->conversionService->convertToGrams($request->quantity, $request->unit);

            $fruit = new Fruit();
            $fruit->setName($request->name);
            $fruit->setQuantity($quantityInGrams);

            $this->fruitRepository->persist($fruit);
            $this->fruitRepository->flush();
            $this->fruitCacheService->invalidateCache();

            return new ApiResponseDTO(true, 'Fruit added successfully', [
                'id'       => $fruit->getId(),
                'name'     => $fruit->getName(),
                'quantity' => $fruit->getQuantity(),
                'unit'     => 'g',
            ]);
        } catch (\Exception $e) {
            return new ApiResponseDTO(false, 'Failed to add fruit: ' . $e->getMessage());
        }
    }

    public function removeFruit(int $fruitId): ApiResponseDTO
    {
        try {
            $fruit = $this->fruitRepository->find($fruitId);

            if (!$fruit) {
                return new ApiResponseDTO(false, 'Fruit not found');
            }

            $this->fruitRepository->remove($fruit);
            $this->fruitRepository->flush();
            $this->fruitCacheService->invalidateCache();

            return new ApiResponseDTO(true, 'Fruit removed successfully');
        } catch (\Exception $e) {
            return new ApiResponseDTO(false, 'Failed to remove fruit: ' . $e->getMessage());
        }
    }

    public function listFruits(?string $unit = 'g'): ApiResponseDTO
    {
        try {
            $fruits = $this->fruitCacheService->findAll();

            $data = array_map(function (Fruit $fruit) use ($unit) {
                $quantity = $fruit->getQuantity();
                if ('kg' === $unit) {
                    $quantity = $this->conversionService->convertToKilograms($quantity);
                }

                return [
                    'id'       => $fruit->getId(),
                    'name'     => $fruit->getName(),
                    'quantity' => $quantity,
                    'unit'     => $unit,
                ];
            }, $fruits);

            return new ApiResponseDTO(true, 'Fruits retrieved successfully', $data);
        } catch (\Exception $e) {
            return new ApiResponseDTO(false, 'Failed to retrieve fruits: ' . $e->getMessage());
        }
    }
}
