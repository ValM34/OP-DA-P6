<?php 

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Message;
use App\Entity\User;

interface MessageServiceInterface
{
  public function findByTrick(int $trick); 
  public function create(User $user, Trick $trick, Message $message): void;
  public function updatePage(int $id);
  public function update(int $id);
  public function delete(int $id);
  public function findById(int $id);
}
