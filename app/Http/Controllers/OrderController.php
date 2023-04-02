<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Services\OrderService;
use App\Services\PaymentUserService;
use Illuminate\Support\Facades\Log;
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
        try {
            $user = $request->user();
            $cart = $request->session()->get('cart');

            if (empty($cart)) {
                return redirect()->back()->withError('No hay productos en el carrito');
            }

            $paymentUser = $this->paymentUserService->getOrCreate($user);
            $order = $this->orderService->create($user, $paymentUser, $cart, $request->payment_method);

            // Clear the cart from session as the order has been created
            $request->session()->forget('cart');

            return redirect()->route('orders.show', $order->public_id);
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->back()->with('error', $th->getMessage());
        }
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
