<?php

namespace App\Services;

use App\Contracts\ConektaRepositoryInterface;
use App\Contracts\GatewayCustomerRepositoryInterface;
use App\Contracts\OpenpayRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\StripeRepositoryInterface;
use App\Models\Order;
use App\Models\User;
use Conekta\Order as ConektaOrder;
use Illuminate\Support\Str;
use Openpay\Resources\OpenpayCharge;
use Stripe\Charge as StripeCharge;
use Stripe\PaymentIntent;
use Conekta\Customer as ConektaCustomer;
use Exception;
use Illuminate\Support\Facades\DB;
use Openpay\Resources\OpenpayCustomer;
use Stripe\Customer as StripeCustomer;

const GATEWAY_PAYMENT_MAP = [
    'oxxo' => 'conekta',
    'card' => 'stripe',
    'bank_account' => 'openpay',
];

/**
 * SHIPPING_COST is an arbitrary value, it's just for demo purposes
 * in the real world, shipping cost should be calculated based on
 * the shipping address and the weight of the products
 * @var float
 */
const SHIPPING_COST = 9.99;

class OrderService
{
    public function __construct(
        private ConektaRepositoryInterface $conektaRepository,
        private GatewayCustomerRepositoryInterface $gatewayCustomerRepository,
        private OpenpayRepositoryInterface $openpayRepository,
        private OrderRepositoryInterface $orderRepository,
        private StripeRepositoryInterface $stripeRepository,
    ) {
    }


    /**
     * @param User $user
     * @param array $cart
     * @param string $payment_method
     * @return Order
     * @throws Exception
     */
    public function create(User $user, array $cart, string $payment_method, $payment_intent = null): Order
    {
        try {
            DB::beginTransaction();

            $subtotal = $this->calculateSubtotal($cart);
            $tax = $this->calculateTax($subtotal);
            // Right now, shipping is an arbitrary value
            $shipping = SHIPPING_COST;
            $total = $this->calculateTotal($subtotal, $tax, $shipping);

            // Validate payment method
            if (!isset(GATEWAY_PAYMENT_MAP[$payment_method])) {
                throw new Exception('Invalid payment method');
            }
            $payment_gateway = GATEWAY_PAYMENT_MAP[$payment_method];

            // Create order
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

            if (!$payment_intent) {
                // Charge order
                $payment = $this->charge($order, $user, $cart, $payment_intent);

                // Update order with payment info
                $order->payment_id = $payment->id;
                if ($payment_method === 'oxxo') {
                    $order->reference = $payment->charges[0]->payment_method->reference;
                }
            } else {
                $order->payment_id = $payment_intent;
                $order->status = 'paid';
            }

            $this->orderRepository->save($order);

            // Associate products with order
            $cartProducts = array_map(function ($item) {
                return [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }, $cart);
            $this->orderRepository->syncProducts($order, $cartProducts);

            DB::commit();

            return $order;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function calculateSubtotal(array $cart): float
    {
        $subtotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
        $subtotal = round($subtotal, 2);
        return $subtotal;
    }


    public function calculateTax(float $subtotal): float
    {
        $tax = round($subtotal * 0.16, 2);
        return $tax;
    }

    private function calculateTotal(float $subtotal, float $tax, float $shipping): float
    {
        $total = round($subtotal + $tax + $shipping, 2);
        return $total;
    }


    /**
     * @param Order $order
     * @param User $user
     * @param array $cart
     * @return OpenpayCharge|ConektaOrder|StripeCharge
     * @throws Exception
     */

    public function charge(
        Order $order,
        User $user,
        array $cart,
        $payment_intent = null
    ): OpenpayCharge | ConektaOrder | StripeCharge {
        $gateway = $order->payment_gateway;
        $customer = $this->getOrCreateCustomer($user, $gateway);
        switch ($gateway) {
            case 'stripe':
                // This isn't reacheable because we already paid for the order in the frontend
                $payment = $this->makeStripeCharge($order, $customer, $cart);
                break;
            case 'openpay':
                $payment = $this->makeOpenpayCharge($order, $customer);
                break;
            case 'conekta':
                $payment = $this->makeConektaCharge($order, $customer, $cart);
                break;
            default:
                throw new Exception('Invalid payment gateway');
        }

        return $payment;
    }


    private function makeConektaCharge(Order $order, ConektaCustomer $customer, $cart): ConektaOrder
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
                "customer_id" => $customer->id,
            ],
            "line_items" => $items,
            "shipping_lines" => [
                [
                    "amount" => (int) round($order->shipping * 100),
                ],
            ],
            // All of this is fake data, it's just for demo purposes
            "shipping_contact" => [
                "phone" => '5555555555',
                "address" => [
                    "postal_code" => '12345',
                    "country" => 'MX',
                    "state" => 'CDMX',
                    "city" => 'CDMX',
                    "street1" => 'Fake address',
                    "residential" => true,
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

    private function makeOpenpayCharge(Order $order, OpenpayCustomer $customer): OpenpayCharge
    {
        $chargeRequest = [
            'method' => $order->payment_method,
            'amount' => $order->total,
            'description' => 'Payment for order #' . $order->public_id,
            'order_id' => $order->public_id,
        ];

        return $this->openpayRepository->makeCharge($chargeRequest, $customer);
    }

    private function makeStripeCharge(Order $order, StripeCustomer $customer, $cart): StripeCharge
    {
        $chargeRequest = [
            'amount' => $order->total * 100,
            'currency' => env('STORE_CURRENCY', 'MXN'),
            'customer' => $customer->id,
            'description' => 'Payment for order #' . $order->public_id,
            'metadata' => [
                'order_id' => $order->public_id,
            ],
        ];

        return $this->stripeRepository->makeCharge($chargeRequest, null);
    }

    public function createStripeIntent(array $data): PaymentIntent
    {
        $customer_id = $this->getOrCreateCustomer($data['user'], 'stripe')->id;

        return $this->stripeRepository->createIntent([
            'amount' => $data['amount'],
            'currency' => env('STORE_CURRENCY', 'MXN'),
            'customer' => $customer_id,
            'automatic_payment_methods' => ['enabled' => true],
        ]);
    }

    private function getOrCreateCustomer(User $user, string $gateway): ConektaCustomer | OpenpayCustomer | StripeCustomer
    {
        $gatewayHandlers = [
            'conekta' => $this->conektaRepository,
            'openpay' => $this->openpayRepository,
            'stripe' => $this->stripeRepository,
        ];

        $handler = $gatewayHandlers[$gateway] ?? null;
        if (!$handler) {
            throw new Exception('Invalid payment gateway');
        }

        $gatewayCustomer = $this->gatewayCustomerRepository->findByUserIdAndGateway($user->id, $gateway);
        if ($gatewayCustomer) {
            return $handler->getCustomer($gatewayCustomer->customer_id);
        }

        $customerData = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        if ($gateway === 'conekta') {
            $customerData['phone'] = $user->phone ?? '5555555555';
        }

        $customer = $handler->createCustomer($customerData);

        $this->gatewayCustomerRepository->create([
            'gateway' => $gateway,
            'customer_id' => $customer->id,
            'user_id' => $user->id,
        ]);

        return $customer;
    }
}
