<?php 

namespace App\Service;

use App\Entity\User;

interface UserServiceInterface
{
  public function findOne(User $user);
}
