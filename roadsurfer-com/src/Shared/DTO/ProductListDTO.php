<?php

declare(strict_types=1);

namespace App\Shared\DTO;

class ProductListDTO
{
    /**
     * @var ProductDTO[]
     */
    private readonly array $products;

    /**
     * @param ProductDTO[] $products
     */
    public function __construct(array $products = [])
    {
        $this->products = $products;
    }

    /**
     * @param ProductDTO[] $products
     */
    public static function create(array $products): self
    {
        return new self($products);
    }

    public function addProduct(ProductDTO $product): self
    {
        $products   = $this->products;
        $products[] = $product;

        return new self($products);
    }

    /**
     * @return ProductDTO[]
     */
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

    /**
     * @param ProductDTO[] $products
     */
    public function setProducts(array $products): self
    {
        return new self($products);
    }
}
