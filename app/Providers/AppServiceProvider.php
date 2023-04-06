<?php

namespace App\Providers;

use App\Contracts\ConektaRepositoryInterface;
use App\Contracts\OpenpayRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\GatewayCustomerRepositoryInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Contracts\StripeRepositoryInterface;
use App\Repositories\ConektaRepository;
use App\Repositories\OpenpayRepository;
use App\Repositories\OrderRepository;
use App\Repositories\GatewayCustomerRepository;
use App\Repositories\ProductRepository;
use App\Repositories\StripeRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ConektaRepositoryInterface::class, ConektaRepository::class);
        $this->app->bind(GatewayCustomerRepositoryInterface::class, GatewayCustomerRepository::class);
        $this->app->bind(OpenpayRepositoryInterface::class, OpenpayRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(StripeRepositoryInterface::class, StripeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
