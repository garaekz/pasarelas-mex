<?php

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private Order $model,
    ) {
        $this->model = $model;
    }

    public function make(array $data): Order
    {
        return $this->model->make($data);
    }

    public function save(Order $order): bool
    {
        return $order->save();
    }

    public function syncProducts(Order $order, array $products): void
    {
        $order->products()->sync($products);
    }
}
