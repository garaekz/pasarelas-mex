<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Services\OrderService;
use App\Services\PaymentUserService;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private PaymentUserService $paymentUserService,
    )
    {
        $this->orderService = $orderService;
        $this->paymentUserService = $paymentUserService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $cart = $request->session()->get('cart', []);
        $subtotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
        $subtotal = round($subtotal, 2);
        $tax = round($subtotal * 0.16, 2);
        $shipping = 9.99;
        $total = round($subtotal + $tax + $shipping, 2);

        $paymentUser = $this->paymentUserService->getOrCreate($request->user());
        $method = $request->payment_method;
        $payment_gateway = $method === 'oxxo' ? 'conekta' : 'openpay';

        $order = Order::make([
            'public_id' => strtolower((string) Str::ulid()),
            'user_id' => $request->user()->id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'payment_method' => $method,
            'payment_gateway' => $payment_gateway,
        ]);

        // Charge the payment method
        $payment = $this->orderService->charge($order, $paymentUser);
        $order->payment_id = $payment->id;
        $order->save();

        $cartProducts = array_map(function ($item) {
            return [
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }, $cart);

        $order->products()->sync($cartProducts);

        $request->session()->forget('cart');

        return redirect()->route('orders.show', $order);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return Inertia::render('Order', [
            'order' => $order->load('products'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
