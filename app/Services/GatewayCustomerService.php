<?php

namespace App\Services;

use App\Contracts\ConektaRepositoryInterface;
use App\Contracts\OpenpayRepositoryInterface;
use App\Contracts\GatewayCustomerRepositoryInterface;
use App\Models\GatewayCustomer;
use App\Models\User;

class GatewayCustomerService
{
  public function __construct(
    private ConektaRepositoryInterface $conektaRepository,
    private GatewayCustomerRepositoryInterface $repository,
    private OpenpayRepositoryInterface $openpayRepository,
  ) {
    $this->repository = $repository;
    $this->conektaRepository = $conektaRepository;
    $this->openpayRepository = $openpayRepository;
  }

  public function getOrCreate(User $user): GatewayCustomer
  {
    $gatewayCustomer = $this->repository->findOne($user->id);
    if (!$gatewayCustomer) {
      $openpayUser = $this->openpayRepository->createCustomer([
        'name' => $user->name,
        'email' => $user->email,
      ]);
      $conektaUser = $this->conektaRepository->createCustomer([
        'name' => $user->name,
        'email' => $user->email,
      ]);
      $gatewayCustomer = $this->repository->create([
        'user_id' => $user->id,
        'openpay_id' => $openpayUser->id,
        'conekta_id' => $conektaUser->id,
      ]);
    }

    return $gatewayCustomer;
  }
}
