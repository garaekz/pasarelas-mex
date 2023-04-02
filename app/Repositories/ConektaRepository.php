<?php

namespace App\Repositories;

use App\Contracts\ConektaRepositoryInterface;
use Conekta\Conekta;
use Conekta\Customer;
use Conekta\Order as ConektaOrder;

class ConektaRepository implements ConektaRepositoryInterface
{
    public function __construct()
    {
        Conekta::setApiKey(env('CONEKTA_SK'));
    }

    public function createCustomer(array $data): Customer
    {
        return Customer::create($data);
    }

    public function getCustomer(string $id): Customer
    {
        return Customer::find($id);
    }

    public function makeCharge(array $data, $customer = null): ConektaOrder
    {
        return ConektaOrder::create($data);
    }
}
