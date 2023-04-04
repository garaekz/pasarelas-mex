<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface GatewayCustomerRepositoryInterface
{
    public function findOne(int $id): Model | null;
    public function findByUserIdAndGateway(int $userId, string $gateway): Model | null;
    public function create(array $data): Model;
}
