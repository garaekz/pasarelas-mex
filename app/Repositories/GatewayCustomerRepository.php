<?php

namespace App\Repositories;

use App\Contracts\GatewayCustomerRepositoryInterface;
use App\Models\GatewayCustomer;

class GatewayCustomerRepository implements GatewayCustomerRepositoryInterface
{
    public function __construct(
        private GatewayCustomer $model,
    ) {
        $this->model = $model;
    }

    public function findOne(int $id): GatewayCustomer | null
    {
        return $this->model->find($id);
    }

    public function findByUserIdAndGateway(int $userId, string $gateway): GatewayCustomer | null
    {
        return $this->model->where('user_id', $userId)->where('gateway', $gateway)->first();
    }

    public function create(array $data): GatewayCustomer
    {
        return $this->model->create($data);
    }
}
