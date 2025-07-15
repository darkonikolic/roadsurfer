<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Shared\DTO\FruitListDTO;
use App\Shared\DTO\VegetableListDTO;
use InvalidArgumentException;

class ImportProductsService
{
    public function __construct(
        private JsonToProductListService $jsonToProductListService,
        private UnitConversionService $unitConversionService,
        private ProductSplitterService $productSplitterService,
        private FruitListManager $fruitListManager,
        private VegetableListManager $vegetableListManager
    ) {}

    /**
     * Process file and return split products
     * 
     * @return array{fruits: FruitListDTO, vegetables: VegetableListDTO}
     */
    public function processFileContent(string $filePath): array
    {
        $this->validateFile($filePath);
        $productListDTO     = $this->loadAndParseFile($filePath);
        $productListInGrams = $this->unitConversionService->convertProductListToGrams($productListDTO);
        
        return $this->productSplitterService->split($productListInGrams);
    }

    /**
     * @return array{importedCount: int, errors: array<string>}
     */
    public function importFromFile(string $filePath): array
    {
        $splitResult = $this->processFileContent($filePath);
        
        if ($this->isEmptyImport($splitResult)) {
            return ['importedCount' => 0, 'errors' => ['No products found in the file.']];
        }
        
        return $this->importProductsToDatabase($splitResult);
    }

    private function validateFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File not found: {$filePath}");
        }
    }

    private function loadAndParseFile(string $filePath): \App\Shared\DTO\ProductListDTO
    {
        $jsonData = file_get_contents($filePath);
        if ($jsonData === false) {
            throw new InvalidArgumentException("Could not read file: {$filePath}");
        }
        
        try {
            return $this->jsonToProductListService->process($jsonData);
        } catch (\Symfony\Component\Validator\Exception\ValidationFailedException $e) {
            $errors = [];
            foreach ($e->getViolations() as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new InvalidArgumentException('Validation failed: ' . implode(', ', $errors));
        }
    }

    /**
     * @param array{fruits: FruitListDTO, vegetables: VegetableListDTO} $splitResult
     */
    private function isEmptyImport(array $splitResult): bool
    {
        $fruitsCount     = count($splitResult['fruits']->getFruits());
        $vegetablesCount = count($splitResult['vegetables']->getVegetables());
        
        return $fruitsCount === 0 && $vegetablesCount === 0;
    }

    /**
     * @param array{fruits: FruitListDTO, vegetables: VegetableListDTO} $splitResult
     *
     * @return array{importedCount: int, errors: array<string>}
     */
    private function importProductsToDatabase(array $splitResult): array
    {
        $importedCount = 0;
        $errors        = [];

        $importedCount += $this->importFruits($splitResult['fruits'], $errors);
        $importedCount += $this->importVegetables($splitResult['vegetables'], $errors);

        return [
            'importedCount' => $importedCount,
            'errors'        => $errors
        ];
    }

    /**
     * @param array<string> $errors
     */
    private function importFruits(FruitListDTO $fruitsList, array &$errors): int
    {
        try {
            $fruitsArray = $fruitsList->getFruits();
            return $this->fruitListManager->importFruits($fruitsArray);
        } catch (\Symfony\Component\Validator\Exception\ValidationFailedException $e) {
            $validationErrors = [];
            foreach ($e->getViolations() as $violation) {
                $validationErrors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            $errors[] = 'Fruits validation failed: ' . implode(', ', $validationErrors);
            return 0;
        } catch (\Exception $e) {
            $errors[] = 'Fruits import failed: ' . $e->getMessage();
            return 0;
        }
    }

    /**
     * @param array<string> $errors
     */
    private function importVegetables(VegetableListDTO $vegetablesList, array &$errors): int
    {
        try {
            $vegetablesArray = $vegetablesList->getVegetables();
            return $this->vegetableListManager->importVegetables($vegetablesArray);
        } catch (\Symfony\Component\Validator\Exception\ValidationFailedException $e) {
            $validationErrors = [];
            foreach ($e->getViolations() as $violation) {
                $validationErrors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            $errors[] = 'Vegetables validation failed: ' . implode(', ', $validationErrors);
            return 0;
        } catch (\Exception $e) {
            $errors[] = 'Vegetables import failed: ' . $e->getMessage();
            return 0;
        }
    }
}
