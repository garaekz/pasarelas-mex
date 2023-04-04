<?php

namespace App\Contracts;

use Stripe\Charge;
use Stripe\Customer;
use Stripe\PaymentIntent;

interface StripeRepositoryInterface extends PaymentGatewayRepositoryInterface
{
    public function createCustomer(array $data): Customer;
    public function getCustomer(string $id): Customer;
    public function makeCharge(array $data, $customer = null): Charge;
    public function createIntent(array $data): PaymentIntent;
}
