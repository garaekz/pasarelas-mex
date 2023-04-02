<?php

namespace App\Contracts;

use Openpay\Resources\OpenpayCharge;
use Openpay\Resources\OpenpayCustomer;

interface OpenpayRepositoryInterface extends PaymentGatewayRepositoryInterface
{
    public function createCustomer(array $data): OpenpayCustomer;
    public function getCustomer(string $id): OpenpayCustomer;
    public function makeCharge(array $data, $customer = null): OpenpayCharge;
}
