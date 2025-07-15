<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Shared\DTO\FruitDTO;
use App\Shared\DTO\FruitListDTO;
use App\Shared\DTO\ProductListDTO;
use App\Shared\DTO\VegetableDTO;
use App\Shared\DTO\VegetableListDTO;

class ProductSplitterService
{
    /**
     * @return array{fruits: FruitListDTO, vegetables: VegetableListDTO}
     */
    public function split(ProductListDTO $productList): array
    {
        $fruits = [];
        $vegetables = [];

        foreach ($productList->getProducts() as $product) {
            if ($product->type === 'fruit') {
                $fruits[] = new FruitDTO(
                    $product->productId,
                    $product->name,
                    $product->quantity,
                    $product->unit
                );
            } elseif ($product->type === 'vegetable') {
                $vegetables[] = new VegetableDTO(
                    $product->productId,
                    $product->name,
                    $product->quantity,
                    $product->unit
                );
            }
        }

        return [
            'fruits' => new FruitListDTO($fruits),
            'vegetables' => new VegetableListDTO($vegetables),
        ];
    }

    public function extractFruits(ProductListDTO $productList): ProductListDTO
    {
        $fruits = [];

        foreach ($productList->getProducts() as $product) {
            if ($product->type === 'fruit') {
                $fruits[] = $product;
            }
        }

        return new ProductListDTO($fruits);
    }

    public function extractVegetables(ProductListDTO $productList): ProductListDTO
    {
        $vegetables = [];

        foreach ($productList->getProducts() as $product) {
            if ($product->type === 'vegetable') {
                $vegetables[] = $product;
            }
        }

        return new ProductListDTO($vegetables);
    }
}
