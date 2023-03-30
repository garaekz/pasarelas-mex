<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface PaymentUserRepositoryInterface
{
    public function findOne(int $id): Model | null;
}
