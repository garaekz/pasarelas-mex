<?php

namespace App\Services;

use App\Contracts\ConektaRepositoryInterface;
use App\Contracts\OpenpayRepositoryInterface;
use App\Models\Order;
use App\Models\PaymentUser;
use Conekta\Charge;
use Openpay\Data\Openpay;
use Openpay\Resources\OpenpayCharge;

class OrderService
{
    public function __construct(
        private ConektaRepositoryInterface $conektaRepository,
        private OpenpayRepositoryInterface $openpayRepository,
    ) {
        $this->conektaRepository = $conektaRepository;
        $this->openpayRepository = $openpayRepository;
    }

    public function createOrder(array $data): array
    {
        $charge = $this->openpayRepository->makeCharge($data);

        return [];
    }

    public function charge(Order $order, PaymentUser $paymentUser): OpenpayCharge | Charge
    {
        $gateway = $order->payment_gateway;
        $data = $order->toArray();
        $data['customer'] = $paymentUser->toArray();
        if ($gateway === 'openpay') {
            return $this->makeOpenpayCharge($order, $paymentUser->openpay_id);
        } elseif ($gateway === 'conekta') {
            return $this->makeConektaCharge($order);
        }
    }

    private function makeConektaCharge (Order $order): array
    {
        $charge = $this->conektaRepository->makeCharge($data);
        return [];
    }

    private function makeOpenpayCharge (Order $order, $customer_id): OpenpayCharge
    {
        $customer = $this->openpayRepository->getCustomer($customer_id);
        $chargeRequest = [
            'method' => $order->payment_method,
            'amount' => $order->total,
            'description' => 'Payment for order #' . $order->public_id,
            'order_id' => $order->public_id,
        ];

        return $this->openpayRepository->makeCharge($chargeRequest, $customer);
    }
}
