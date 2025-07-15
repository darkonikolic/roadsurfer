<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Shared\DTO\ProductDTO;
use App\Shared\DTO\ProductListDTO;
use InvalidArgumentException;

class JsonToProductListService
{
    public function process(string $json): ProductListDTO
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new InvalidArgumentException('JSON must be an array');
        }

        $products = [];

        foreach ($data as $item) {
            $products[] = $this->createProductDTO($item);
        }

        return new ProductListDTO($products);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function createProductDTO(array $item): ProductDTO
    {
        $this->validateRequiredFields($item);
        $this->validateQuantity($item);
        $this->validateUnit($item);
        $this->validateType($item);

        return new ProductDTO(
            $item['id'] ?? null,
            $item['name'],
            $item['type'],
            (float)$item['quantity'],
            $item['unit']
        );
    }

    /**
     * @param array<string, mixed> $item
     */
    private function validateRequiredFields(array $item): void
    {
        $requiredFields = ['name', 'type', 'quantity', 'unit'];

        foreach ($requiredFields as $field) {
            if (!isset($item[$field])) {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function validateQuantity(array $item): void
    {
        if (!is_numeric($item['quantity'])) {
            throw new InvalidArgumentException('Quantity must be a number');
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function validateUnit(array $item): void
    {
        $validUnits = ['kg', 'g'];

        if (!in_array($item['unit'], $validUnits, true)) {
            throw new InvalidArgumentException('Unit must be kg or g');
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function validateType(array $item): void
    {
        $validTypes = ['fruit', 'vegetable'];

        if (!in_array($item['type'], $validTypes, true)) {
            throw new InvalidArgumentException('Type must be fruit or vegetable');
        }
    }
}
