<?php

namespace Tests\Feature\Controllers;

use App\Models\Order;
use App\Models\PaymentUser;
use App\Models\User;
use Conekta\Charge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Openpay\Resources\OpenpayCharge;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test it can create an order.
     */
    public function test_it_can_create_an_order()
    {
        $user = User::factory()->create();
        $this->mock(\App\Services\OrderService::class, function ($mock) use ($user) {
            $mock->shouldReceive('charge')->once()->andReturn(new Charge('charge_id'));
        });
        $this->mock(\App\Services\PaymentUserService::class, function ($mock) use ($user) {
            $mock->shouldReceive('getOrCreate')->once()->andReturn(PaymentUser::factory()->create([
                'user_id' => $user->id,
                'openpay_id' => 'openpay_id',
                'conekta_id' => 'conekta_id',
            ]));
        });

        $this->actingAs($user, "web");
        $this->post('/orders', [
            'payment_method' => 'bank_account',
        ])->assertRedirectContains('/orders/');
    }

    /**
     * Test it can show an order.
     */
    public function test_it_can_show_an_order()
    {
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
    }
}
