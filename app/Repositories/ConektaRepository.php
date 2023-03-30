<?php

namespace App\Repositories;

use App\Contracts\ConektaRepositoryInterface;
use Conekta\Charge;
use Openpay\Data\Openpay;
use Conekta\Conekta;
use Conekta\Customer;

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

    public function makeCharge(array $data, $customer = null): Charge
    {
        return Charge::create($data);
    }
}
