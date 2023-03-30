<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $subtotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        return Inertia::render('Cart', [
            'cart' => $cart,
            'subtotal' => round($subtotal, 2),
            'tax' => round($subtotal * 0.16, 2),
            'shipping' => 9.99,
            'total' => round($subtotal * 1.16 + 9.99, 2),
        ]);
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        $subtotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        return Inertia::render('Checkout', [
            'subtotal' => round($subtotal, 2),
            'tax' => round($subtotal * 0.16, 2),
            'shipping' => 9.99,
            'total' => round($subtotal * 1.16 + 9.99, 2),
        ]);
    }

    public function add(AddCartRequest $request)
    {
        $product = Product::find($request->product_id);
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
    }
}
