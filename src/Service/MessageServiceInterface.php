<?php 

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Message;
use App\Entity\User;

interface MessageServiceInterface
{
  public function findByTrick(int $trick, User $user): array; 
  public function create(User $user, Trick $trick, Message $message): void;
  public function updatePage(Message $message): array;
  public function update(Message $message): Message;
  public function delete(int $id): void;
}
