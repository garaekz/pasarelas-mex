<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\Product;

interface ProductRepositoryInterface
{
    public function find(int $id): Product;
}
