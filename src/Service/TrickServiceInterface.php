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
  public function findAllCategories(); // Envoyer dans categoryService (findAll())
}
