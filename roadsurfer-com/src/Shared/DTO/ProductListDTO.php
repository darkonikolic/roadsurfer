<?php

declare(strict_types=1);

namespace App\Shared\DTO;

readonly class ProductListDTO
{
    /**
     * @param ProductDTO[] $products
     */
    public function __construct(
        public array $products = []
    ) {
    }

    public static function create(array $products = []): self
    {
        return new self($products);
    }

    public function addProduct(ProductDTO $product): self
    {
        $products = $this->products;
        $products[] = $product;

        return new self($products);
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function count(): int
    {
        return count($this->products);
    }

    public function isEmpty(): bool
    {
        return empty($this->products);
    }
}
