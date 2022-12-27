<?php 

namespace App\Service;

use App\Entity\Trick;

interface TrickServiceInterface
{
  public function create($user, Trick $trick);
  public function findAll();
  public function findOne(int $id);
  public function updatePage(int $id); // Fusionner dans update
  public function update(Trick $trick);
  public function delete(int $id);
  public function getMessages($id); // Envoyer dans messageService (findAll())
  public function findAllCategories(); // Envoyer dans categoryService (findAll())
  public function createMessage($user, $trick, $message); // Envoyer dans messageService (create())
  public function updateMessagePage(int $id); // Fusionner dans updateMessage
  public function updateMessage(int $id); // Envoyer dans message et nommer (update())
  public function deleteMessage(int $id); // Envoyer dans messageService et nommer (delete())
}
