<?php

namespace App\Contracts;

use Conekta\Customer;
use Conekta\Order as ConektaOrder;

interface ConektaRepositoryInterface extends PaymentGatewayRepositoryInterface
{
    public function createCustomer(array $data): Customer;
    public function getCustomer(string $id): Customer;
    public function makeCharge(array $data, $customer = null): ConektaOrder;
}
