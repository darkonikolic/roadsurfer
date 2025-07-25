<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\External\Cache\VegetableCacheService;
use App\Infrastructure\Persistence\Entity\Vegetable;
use App\Infrastructure\Persistence\Repository\VegetableRepository;
use App\Shared\DTO\ApiResponseDTO;
use App\Shared\DTO\VegetableApiRequestDTO;
use App\Shared\DTO\VegetableDTO;

class VegetableManagementService
{
    public function __construct(
        private readonly VegetableRepository $vegetableRepository,
        private readonly VegetableCacheService $vegetableCacheService,
        private readonly UnitConversionService $conversionService,
    ) {
    }

    public function addVegetable(VegetableApiRequestDTO $request): ApiResponseDTO
    {
        try {
            $quantityInGrams = $this->conversionService->convertToGrams($request->quantity, $request->unit);

            $vegetable = new Vegetable();
            $vegetable->setName($request->name);
            $vegetable->setQuantity($quantityInGrams);

            $this->vegetableRepository->persist($vegetable);
            $this->vegetableRepository->flush();
            $this->vegetableCacheService->invalidateCache();

            return new ApiResponseDTO(true, 'Vegetable added successfully', [
                'id'       => $vegetable->getId(),
                'name'     => $vegetable->getName(),
                'quantity' => $vegetable->getQuantity(),
                'unit'     => 'g',
            ]);
        } catch (\Exception $e) {
            return new ApiResponseDTO(false, 'Failed to add vegetable: ' . $e->getMessage());
        }
    }

    public function removeVegetable(int $vegetableId): ApiResponseDTO
    {
        try {
            $vegetable = $this->vegetableRepository->find($vegetableId);

            if (!$vegetable) {
                return new ApiResponseDTO(false, 'Vegetable not found');
            }

            $this->vegetableRepository->remove($vegetable);
            $this->vegetableRepository->flush();
            $this->vegetableCacheService->invalidateCache();

            return new ApiResponseDTO(true, 'Vegetable removed successfully');
        } catch (\Exception $e) {
            return new ApiResponseDTO(false, 'Failed to remove vegetable: ' . $e->getMessage());
        }
    }

    public function listVegetables(?string $unit = 'g'): ApiResponseDTO
    {
        try {
            $vegetables = $this->vegetableCacheService->findAll();

            $data = array_map(function (Vegetable $vegetable) use ($unit) {
                $quantity = $vegetable->getQuantity();
                if ('kg' === $unit) {
                    $quantity = $this->conversionService->convertToKilograms($quantity);
                }

                return [
                    'id'       => $vegetable->getId(),
                    'name'     => $vegetable->getName(),
                    'quantity' => $quantity,
                    'unit'     => $unit,
                ];
            }, $vegetables);

            return new ApiResponseDTO(true, 'Vegetables retrieved successfully', $data);
        } catch (\Exception $e) {
            return new ApiResponseDTO(false, 'Failed to retrieve vegetables: ' . $e->getMessage());
        }
    }
}
