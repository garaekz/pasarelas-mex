<?php

namespace App\Repositories;

use App\Contracts\PaymentUserRepositoryInterface;
use App\Models\PaymentUser;

class PaymentUserRepository implements PaymentUserRepositoryInterface
{
    public function __construct(
        private PaymentUser $model,
    ) {
        $this->model = $model;
    }

    public function findOne(int $id): PaymentUser | null
    {
        return $this->model->find($id);
    }

    public function create(array $data): PaymentUser
    {
        return $this->model->create($data);
    }
}
