<?php

use App\Models\Order;
use App\Models\PaymentUser;
use App\Models\User;
use App\Services\OrderService;

test('it can show the order to the owner', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user, "web");
    $this->get('/orders/' . $order->public_id)
        ->assertInertia(fn ($page) => $page
            ->component('Order')
            ->has('order')
        );
});

test('it cannot show the order to a non-owner', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create();

    $this->actingAs($user, "web");
    $this->get('/orders/' . $order->public_id)
        ->assertForbidden();
});

test('it cannot show the order to a guest', function () {
    $order = Order::factory()->create();

    $this->get('/orders/' . $order->public_id)
        ->assertRedirect('/login');
});

test('it can create an order', function () {
    $user = User::factory()->create();
    $this->mock(OrderService::class, function ($mock) use ($user) {
        $mock->shouldReceive('create')->once()->andReturn(Order::factory()->create([
            'user_id' => $user->id,
        ]));
    });
    $this->mock(\App\Services\PaymentUserService::class, function ($mock) use ($user) {
        $mock->shouldReceive('getOrCreate')->once()->andReturn(PaymentUser::factory()->create([
            'user_id' => $user->id,
            'openpay_id' => 'openpay_id',
            'conekta_id' => 'conekta_id',
        ]));
    });

    $this->actingAs($user, "web");
    $this->withSession(['cart' => [
        [
            'id' => 1,
            'name' => 'Product 1',
            'price' => 100,
            'quantity' => 3,
        ],
        [
            'id' => 2,
            'name' => 'Product 2',
            'price' => 25.85,
            'quantity' => 2,
        ]
    ]]);
    $this->post('/orders', [
        'payment_method' => 'bank_account',
    ])->assertRedirectContains('/orders/');
    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
    ]);
});

test('it clears the session after placing order', function () {
    $user = User::factory()->create();
    $this->mock(OrderService::class, function ($mock) use ($user) {
        $mock->shouldReceive('create')->once()->andReturn(Order::factory()->create([
            'user_id' => $user->id,
        ]));
    });
    $this->mock(\App\Services\PaymentUserService::class, function ($mock) use ($user) {
        $mock->shouldReceive('getOrCreate')->once()->andReturn(PaymentUser::factory()->create([
            'user_id' => $user->id,
            'openpay_id' => 'openpay_id',
            'conekta_id' => 'conekta_id',
        ]));
    });

    $this->actingAs($user, "web");
    $this->withSession(['cart' => [
        [
            'id' => 1,
            'name' => 'Product 1',
            'price' => 100,
            'quantity' => 3,
        ],
        [
            'id' => 2,
            'name' => 'Product 2',
            'price' => 25.85,
            'quantity' => 2,
        ]
    ]]);
    $this->post('/orders', [
        'payment_method' => 'bank_account',
    ])->assertSessionMissing('cart');
});
