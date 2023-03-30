<?php

namespace App\Services;

use App\Contracts\ConektaRepositoryInterface;
use App\Contracts\OpenpayRepositoryInterface;
use App\Contracts\PaymentUserRepositoryInterface;
use App\Models\PaymentUser;
use App\Models\User;

class PaymentUserService
{
  public function __construct(
    private ConektaRepositoryInterface $conektaRepository,
    private PaymentUserRepositoryInterface $repository,
    private OpenpayRepositoryInterface $openpayRepository,
  ) {
    $this->repository = $repository;
    $this->conektaRepository = $conektaRepository;
    $this->openpayRepository = $openpayRepository;
  }

  public function getOrCreate(User $user): PaymentUser
  {
    $paymentUser = $this->repository->findOne($user->id);
    if (!$paymentUser) {
      $openpayUser = $this->openpayRepository->createCustomer([
        'name' => $user->name,
        'email' => $user->email,
      ]);
      $conektaUser = $this->conektaRepository->createCustomer([
        'name' => $user->name,
        'email' => $user->email,
      ]);
      $paymentUser = $this->repository->create([
        'user_id' => $user->id,
        'openpay_id' => $openpayUser->id,
        'conekta_id' => $conektaUser->id,
      ]);
    }

    return $paymentUser;
  }
}
