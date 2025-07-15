<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\Persistence\Entity\Vegetable;
use App\Infrastructure\Persistence\Repository\VegetableRepository;
use App\Shared\DTO\VegetableDTO;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VegetableListManager
{
    public function __construct(
        private VegetableRepository $vegetableRepository,
        private ValidatorInterface $validator
    ) {}

    /**
     * Import list of vegetables into database
     * 
     * @param array<VegetableDTO> $vegetables
     * 
     * @return int Number of imported vegetables
     */
    public function importVegetables(array $vegetables): int
    {
        if (empty($vegetables)) {
            return 0;
        }

        $importedCount = 0;

        foreach ($vegetables as $vegetableDTO) {
            $this->validateVegetableDTO($vegetableDTO);
            $entity = $this->mapVegetableDTOToEntity($vegetableDTO);
            $this->vegetableRepository->persist($entity);
            $importedCount++;
        }

        // Flush all at once
        $this->vegetableRepository->flush();

        return $importedCount;
    }

    /**
     * Validate VegetableDTO before mapping to entity
     */
    private function validateVegetableDTO(VegetableDTO $vegetableDTO): void
    {
        $violations = $this->validator->validate($vegetableDTO);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            
            throw new ValidationFailedException($vegetableDTO, $violations);
        }
    }

    /**
     * Map VegetableDTO to Vegetable entity
     */
    private function mapVegetableDTOToEntity(VegetableDTO $vegetableDTO): Vegetable
    {
        $entity = new Vegetable();
        $entity->setName($vegetableDTO->name);
        $entity->setQuantity($vegetableDTO->quantity);
        
        return $entity;
    }
}
