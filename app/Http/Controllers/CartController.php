<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;

const SHIPPING_COST = 9.99;

class CartController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private ProductService $productService,
    ) {
    }
    public function index()
    {
        $cart = session()->get('cart', []);
        $subtotal = $this->orderService->calculateSubtotal($cart);
        $tax = $this->orderService->calculateTax($subtotal);
        // $shipping should be calculated based on the shipping address and the weight of the products
        // but for demo purposes, we'll just use a constant
        $shipping = SHIPPING_COST;

        return Inertia::render('Cart', [
            'cart' => $cart,
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'shipping' => $shipping,
            'total' => round($subtotal + $tax + $shipping, 2),
        ]);
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->withError('Your cart is empty');
        }

        $subtotal = $this->orderService->calculateSubtotal($cart);
        $tax = $this->orderService->calculateTax($subtotal);
        // $shipping should be calculated based on the shipping address and the weight of the products
        // but for demo purposes, we'll just use a constant
        $shipping = SHIPPING_COST;
        $total = round($subtotal + $tax + $shipping, 2);

        return Inertia::render('Checkout', [
            'stripe_key' => env('STRIPE_PK'),
            'stripe_intent' => $this->orderService->createStripeIntent([
                'amount' => $total * 100,
                'currency' => 'mxn',
                'user' => auth()->user(),
            ]),
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    public function add(AddCartRequest $request)
    {
        try {
            $product = $this->productService->find($request->product_id);
            $cart = $request->session()->get('cart', []);

            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity']++;
            } else {
                $cart[$product->id] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'quantity' => 1,
                ];
            }

            $request->session()->put('cart', $cart);

            return redirect()->route('welcome');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function remove(Request $request, $id)
    {
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }
}
