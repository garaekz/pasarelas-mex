<?php

namespace App\Services;

use App\Contracts\ConektaRepositoryInterface;
use App\Contracts\OpenpayRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\PaymentUser;
use App\Models\User;
use Conekta\Order as ConektaOrder;
use Illuminate\Support\Str;
use Openpay\Resources\OpenpayCharge;
class OrderService
{
    public function __construct(
        private ConektaRepositoryInterface $conektaRepository,
        private OpenpayRepositoryInterface $openpayRepository,
        private OrderRepositoryInterface $orderRepository,
    ) {
        $this->conektaRepository = $conektaRepository;
        $this->openpayRepository = $openpayRepository;
    }

    public function create(User $user, PaymentUser $paymentUser, array $cart, string $payment_method): Order
    {
        $subtotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
        $subtotal = round($subtotal, 2);
        $tax = round($subtotal * 0.16, 2);
        // Right now, shipping is an arbitrary value
        $shipping = 9.99;
        $total = round($subtotal + $tax + $shipping, 2);
        $payment_gateway = $payment_method === 'oxxo' ? 'conekta' : 'openpay';

        $order = $this->orderRepository->make([
            'public_id' => strtolower((string) Str::ulid()),
            'user_id' => $user->id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'payment_method' => $payment_method,
            'payment_gateway' => $payment_gateway,
        ]);

        $payment = $this->charge($order, $paymentUser, $cart);

        $order->payment_id = $payment->id;
        $this->orderRepository->save($order);

        $cartProducts = array_map(function ($item) {
            return [
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }, $cart);

        $this->orderRepository->syncProducts($order, $cartProducts);

        return $order;
    }

    public function charge(Order $order, PaymentUser $paymentUser, array $cart): OpenpayCharge | ConektaOrder
    {
        $gateway = $order->payment_gateway;
        $data = $order->toArray();
        $data['customer'] = $paymentUser->toArray();
        if ($gateway === 'openpay') {
            return $this->makeOpenpayCharge($order, $paymentUser->openpay_id);
        } else {
            return $this->makeConektaCharge($order, $paymentUser->conekta_id, $cart);
        }
    }

    private function makeConektaCharge (Order $order, $customer_id, $cart): ConektaOrder
    {
        // Conekta expects a properly formatted array of items if not it parses an object and throws an error
        $items = [];

        foreach ($cart as $item) {
            $items[] = [
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => (int) round($item['price'] * 100, 0),
            ];
        }

        $chargeRequest = [
            "currency" => env('STORE_CURRENCY', 'MXN'),
            "customer_info" => [
                "customer_id" => $customer_id,
            ],
            "line_items" => $items,
            "shipping_lines" => [
                [
                    "amount" => (int) round($order->shipping * 100),
                ],
            ],
            "tax_lines" => [
                [
                    "amount" => (int) round($order->tax * 100),
                    "description" => "IVA",
                ],
            ],
            "charges" => [
                [
                    "payment_method" => [
                        "type" => $order->payment_method === 'oxxo' ? 'cash' : $order->payment_method,
                        "expires_at" => $order->payment_method === 'oxxo' ? now()->addDays(env('STORE_PAYMENT_EXPIRATION_DAYS', 30))->timestamp : null,
                    ],
                    // Conekta expects the amount in cents and rounded
                    "amount" => (int) round($order->total * 100),
                ],
            ],
        ];

        return $this->conektaRepository->makeCharge($chargeRequest);
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
