<?php

namespace App\Contracts;

interface PaymentGatewayRepositoryInterface
{
    public function createCustomer(array $data);
    public function getCustomer(string $id);
    public function makeCharge(array $data, $customer = null);
}
