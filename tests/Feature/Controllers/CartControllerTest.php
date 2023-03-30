<?php

namespace Tests\Feature;

use App\Http\Requests\AddCartRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test it can show the cart page.
     */
    public function test_it_can_show_the_cart_page()
    {
        $this->get('/cart')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Cart')
                ->has('cart')
                ->has('subtotal')
                ->has('tax')
                ->has('shipping')
                ->has('total')
            );
    }

    /**
     * Test it can show the checkout page.
     */
    public function test_it_can_show_the_checkout_page()
    {
        $this->actingAs(User::factory()->create(), "web");
        $this->get('/checkout')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Checkout')
                ->has('subtotal')
                ->has('tax')
                ->has('shipping')
                ->has('total')
            );
    }

    /**
     * Test it won't show the checkout page if the user is not logged in.
     */
    public function test_it_wont_show_the_checkout_page_if_the_user_is_not_logged_in()
    {
        $this->get('/checkout')
            ->assertRedirect('/login');
    }

    /**
     * Test it can add a product to the cart.
     */
    public function test_it_can_add_a_product_to_the_cart()
    {
        Product::factory()->create([
            'id' => 1,
            'name' => 'Test Product',
            'price' => 9.99,
            'image' => 'test.jpg',
        ]);

        $this->post('/cart', [
            'product_id' => 1,
        ])
            ->assertRedirect('/')
            ->assertSessionHas('cart', [
                1 => [
                    'id' => 1,
                    'name' => 'Test Product',
                    'price' => 9.99,
                    'image' => 'test.jpg',
                    'quantity' => 1,
                ],
            ]);
    }

    /**
     * Test it can increment the quantity of a product in the cart.
     */
    public function test_it_can_increment_the_quantity_of_a_product_in_the_cart()
    {
        Product::factory()->create([
            'id' => 1,
            'name' => 'Test Product',
            'price' => 9.99,
            'image' => 'test.jpg',
        ]);

        $this->post('/cart', [
            'product_id' => 1,
        ]);

        $this->post('/cart', [
            'product_id' => 1,
        ])
            ->assertRedirect('/')
            ->assertSessionHas('cart', [
                1 => [
                    'id' => 1,
                    'name' => 'Test Product',
                    'price' => 9.99,
                    'image' => 'test.jpg',
                    'quantity' => 2,
                ],
            ]);
    }

    /**
     * Test it can validate the add cart request.
     */
    public function test_it_can_validate_the_add_cart_request()
    {
        $this->post('/cart', [
            'product_id' => null,
        ])
            ->assertSessionHasErrors('product_id');
    }

    /**
     * Test it returns a cart count in every request.
     */
    public function test_it_returns_a_cart_count_in_every_request()
    {
        Product::factory()->create();

        $this->get('/')
            ->assertInertia(fn (Assert $page) => $page
                ->has('cartCount')
                ->where('cartCount', 0)
            );


        $this->post('/cart', [
            'product_id' => 1,
        ]);

        $this->get('/')
            ->assertInertia(fn (Assert $page) => $page
                ->has('cartCount')
                ->where('cartCount', 1)
            );
    }
}
