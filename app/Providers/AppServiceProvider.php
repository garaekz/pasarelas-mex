<?php

namespace App\Providers;

use App\Contracts\ConektaRepositoryInterface;
use App\Contracts\OpenpayRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\PaymentUserRepositoryInterface;
use App\Repositories\ConektaRepository;
use App\Repositories\OpenpayRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ConektaRepositoryInterface::class, ConektaRepository::class);
        $this->app->bind(OpenpayRepositoryInterface::class, OpenpayRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(PaymentUserRepositoryInterface::class, PaymentUserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
