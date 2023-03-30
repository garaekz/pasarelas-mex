<?php

namespace App\Contracts;

use Conekta\Customer;

interface ConektaRepositoryInterface extends PaymentGatewayRepositoryInterface
{
    public function createCustomer(array $data): Customer;
    public function getCustomer(string $id): Customer;
}
