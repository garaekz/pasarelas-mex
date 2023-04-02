<?php

namespace App\Contracts;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function make(array $data): Order;
    public function save(Order $order): bool;
    public function syncProducts(Order $order, array $products): void;
}
