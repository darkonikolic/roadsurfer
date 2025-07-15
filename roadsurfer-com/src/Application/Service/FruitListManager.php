<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\Persistence\Entity\Fruit;
use App\Infrastructure\Persistence\Repository\FruitRepository;
use App\Shared\DTO\FruitDTO;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FruitListManager
{
    public function __construct(
        private FruitRepository $fruitRepository,
        private ValidatorInterface $validator
    ) {}

    /**
     * Import list of fruits into database
     * 
     * @param array<FruitDTO> $fruits
     * 
     * @return int Number of imported fruits
     */
    public function importFruits(array $fruits): int
    {
        if (empty($fruits)) {
            return 0;
        }

        $importedCount = 0;

        foreach ($fruits as $fruitDTO) {
            $this->validateFruitDTO($fruitDTO);
            $entity = $this->mapFruitDTOToEntity($fruitDTO);
            $this->fruitRepository->persist($entity);
            $importedCount++;
        }

        // Flush all at once
        $this->fruitRepository->flush();

        return $importedCount;
    }

    /**
     * Validate FruitDTO before mapping to entity
     */
    private function validateFruitDTO(FruitDTO $fruitDTO): void
    {
        $violations = $this->validator->validate($fruitDTO);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            
            throw new ValidationFailedException($fruitDTO, $violations);
        }
    }

    /**
     * Map FruitDTO to Fruit entity
     */
    private function mapFruitDTOToEntity(FruitDTO $fruitDTO): Fruit
    {
        $entity = new Fruit();
        $entity->setName($fruitDTO->name);
        $entity->setQuantity($fruitDTO->quantity);
        
        return $entity;
    }
}
