<?php

namespace App\Service;

use App\Entity\User;

interface SendMailServiceInterface
{
  public function emailValidation(string $from, string $to, string $subject, string $token): void;
  public function passwordRecovery(User $user, string $token): void;
}