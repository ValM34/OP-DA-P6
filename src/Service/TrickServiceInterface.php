<?php 

namespace App\Service;

use App\Entity\Trick;

interface TrickServiceInterface
{
  public function create($user, Trick $trick);
  public function findAll();
  public function findOne(int $id);
  public function updatePage(int $id);
  public function update(int $id);
  public function delete(int $id);
  public function getMessages($id);
  public function findAllCategories();
  public function createMessage($user, $trick, $message);
  public function updateMessagePage(int $id);
  public function updateMessage(int $id);
  public function deleteMessage(int $id);
}
