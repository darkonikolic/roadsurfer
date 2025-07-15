<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Shared\DTO\ProductDTO;
use App\Shared\DTO\ProductListDTO;

class UnitConversionService
{
    private const KG_TO_G = 1000;

    public function convertProductListToGrams(ProductListDTO $productList): ProductListDTO
    {
        $convertedProducts = [];

        foreach ($productList->getProducts() as $product) {
            $convertedProducts[] = $this->convertProductToGrams($product);
        }

        return new ProductListDTO($convertedProducts);
    }

    public function convertToGrams(float $quantity, string $unit): float
    {
        if ($unit === 'kg') {
            return $quantity * self::KG_TO_G;
        }

        return $quantity; // Already in grams
    }

    public function convertToKilograms(float $quantity): float
    {
        return $quantity / self::KG_TO_G;
    }

    private function convertProductToGrams(ProductDTO $product): ProductDTO
    {
        $quantity = $product->quantity;
        $unit = $product->unit;

        if ($unit === 'kg') {
            $quantity = $quantity * self::KG_TO_G;
            $unit = 'g';
        }

        return new ProductDTO(
            $product->productId,
            $product->name,
            $product->type,
            $quantity,
            $unit
        );
    }
}
