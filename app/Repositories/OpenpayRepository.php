<?php

namespace App\Repositories;

use App\Contracts\OpenpayRepositoryInterface;
use Openpay\Data\Openpay;
use Openpay\Resources\OpenpayCharge;
use Openpay\Resources\OpenpayCustomer;
use Openpay\Resources\OpenpayCustomerList;

class OpenpayRepository implements OpenpayRepositoryInterface
{
    private OpenpayCustomerList $instance;
    public function __construct()
    {
        $instance = Openpay::getInstance(
            env('OPENPAY_ID'),
            env('OPENPAY_SK')
        );

        $this->instance = $instance->customers;
    }

    public function createCustomer(array $data): OpenpayCustomer
    {
        return $this->instance->add($data);
    }

    public function getCustomer(string $id): OpenpayCustomer
    {
        return $this->instance->get($id);
    }

    public function makeCharge(array $data, $customer = null): OpenpayCharge
    {
        return $customer->charges->create($data);
    }
}
