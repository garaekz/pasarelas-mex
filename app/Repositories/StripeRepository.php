<?php

namespace App\Repositories;

use App\Contracts\StripeRepositoryInterface;
use Conekta\Conekta;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripeRepository implements StripeRepositoryInterface
{
    private $client;
    public function __construct()
    {
        $this->client = new StripeClient(env('STRIPE_SK'));
    }

    public function createCustomer(array $data): Customer
    {
        return $this->client->customers->create($data);
    }

    public function getCustomer(string $id): Customer
    {
        return $this->client->customers->retrieve($id);
    }

    public function makeCharge(array $data, $customer = null): Charge
    {
        return $this->client->charges->create($data);
    }

    public function createIntent(array $data): PaymentIntent
    {
        return $this->client->paymentIntents->create($data);
    }
}
