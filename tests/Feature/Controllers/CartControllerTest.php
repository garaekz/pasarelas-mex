<?php

use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Inertia\Testing\AssertableInertia as Assert;
use Stripe\PaymentIntent;

test('it can show cart page', function () {
    $this->get('/cart')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
                ->component('Cart')
                ->has('cart')
                ->has('subtotal')
                ->has('tax')
                ->has('shipping')
                ->has('total')
            );
});

test('it can show checkout page', function () {
    $this->mock(OrderService::class, function ($mock) {
        $mock->shouldReceive('calculateSubtotal')
            ->once()
            ->andReturn(100);
        $mock->shouldReceive('calculateTax')
            ->once()
            ->andReturn(16);
        $mock->shouldReceive('createStripeIntent')
            ->once()
            ->andReturn(new PaymentIntent());
    });

    // Create cart in session so it won't redirect back
    session()->put('cart', [
        1 => [
            'id' => 1,
            'name' => 'Test Product',
            'price' => 100,
            'image' => 'test.jpg',
            'quantity' => 1,
        ],
    ]);

    $this->actingAs(User::factory()->create(), "web");
    $this->get('/checkout')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
                ->component('Checkout')
                ->has('subtotal')
                ->has('tax')
                ->has('shipping')
                ->has('total')
            );
});

test('it redirects back if cart is empty', function () {
    $this->actingAs(User::factory()->create(), "web")
        ->get('/checkout')
        ->assertRedirect('/');
});

test('it wont show checkout page if user is not logged in', function () {
    $this->get('/checkout')
        ->assertRedirect('/login');
});

test('it can add product to cart', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'price' => 100,
    ]);

    $this->post('/cart', [
        'product_id' => $product->id,
        'quantity' => 1,
    ])
        ->assertRedirect('/')
        ->assertSessionHas('cart', [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => 1,
            ],
        ]);
});

test('it can update product quantity in cart', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'price' => 100,
    ]);

    $this->post('/cart', [
        'product_id' => $product->id,
    ]);

    $this->post('/cart', [
        'product_id' => $product->id,
    ])
        ->assertRedirect('/')
        ->assertSessionHas('cart', [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => 2,
            ],
        ]);
});

test('it throws validation error if product does not exist', function () {
    $this->post('/cart', [
        'product_id' => 1,
    ])
        ->assertRedirect('/')
        ->assertSessionHasErrors('product_id');
});

test('it returns a cart count on every request', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
                ->has('cartCount')
                ->where('cartCount', 0)
            );

    $product = Product::factory()->create();

    $this->post('/cart', [
        'product_id' => $product->id,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
                ->has('cartCount')
                ->where('cartCount', 1)
            );
});

it('it can remove product from cart', function () {
    $product = Product::factory()->create();

    $this->post('/cart', [
        'product_id' => $product->id,
    ]);

    $this->delete('/cart/' . $product->id)
        ->assertRedirect('/cart')
        ->assertSessionMissing('cart.' . $product->id);
});
