<?php 

namespace App\Service;

use App\Entity\Trick;

interface MessageServiceInterface
{
  public function findByTrick($id); 
  public function create($user, $trick, $message);
  public function updatePage(int $id);
  public function update(int $id);
  public function delete(int $id);
  public function findById(int $id);
}
