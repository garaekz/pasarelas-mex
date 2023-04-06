<?php

namespace App\Services;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function find(int $id): Product
    {
        return $this->productRepository->find($id);
    }

    public function getProduct(int $id): Product
    {
        return $this->productRepository->getProduct($id);
    }

    public function createProduct(array $data): Product
    {
        return $this->productRepository->createProduct($data);
    }

    public function updateProduct(int $id, array $data): Product
    {
        return $this->productRepository->updateProduct($id, $data);
    }

    public function deleteProduct(int $id): void
    {
        $this->productRepository->deleteProduct($id);
    }
}
