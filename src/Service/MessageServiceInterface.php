<?php 

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Message;
use App\Entity\User;

interface MessageServiceInterface
{
  public function findByTrick(int $trick): array; 
  public function findById(int $id): Message;
  public function create(User $user, Trick $trick, Message $message): void;
  public function updatePage(int $id): array;
  public function update(int $id): Message;
  public function delete(int $id): void;
}
