<?php

namespace App\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private Product $model,
    ) {}
    public function find(int $id): Product
    {
        return $this->model->find($id);
    }
}
