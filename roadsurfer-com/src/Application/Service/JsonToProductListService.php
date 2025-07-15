<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Shared\DTO\ProductDTO;
use App\Shared\DTO\ProductListDTO;
use InvalidArgumentException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonToProductListService
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
    }

    public function process(string $json): ProductListDTO
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new InvalidArgumentException('JSON must be an array');
        }

        $products = [];

        foreach ($data as $item) {
            $productDTO = $this->createProductDTO($item);
            $products[] = $productDTO;
        }

        $productListDTO = new ProductListDTO($products);
        
        // Validate the entire ProductListDTO
        $this->validateProductList($productListDTO);

        return $productListDTO;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function createProductDTO(array $item): ProductDTO
    {
        // Create ProductDTO from array data
        $productDTO = new ProductDTO(
            $item['id'] ?? null,
            $item['name'],
            $item['type'],
            (float)$item['quantity'],
            $item['unit']
        );

        // Validate the ProductDTO
        $this->validateProduct($productDTO);

        return $productDTO;
    }

    private function validateProduct(ProductDTO $productDTO): void
    {
        $violations = $this->validator->validate($productDTO);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            
            throw new ValidationFailedException($productDTO, $violations);
        }
    }

    private function validateProductList(ProductListDTO $productListDTO): void
    {
        $violations = $this->validator->validate($productListDTO);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            
            throw new ValidationFailedException($productListDTO, $violations);
        }
    }
}
