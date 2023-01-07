<?php 

namespace App\Service;

use App\Entity\Trick;

interface TrickServiceInterface
{
  public function create($user, Trick $trick, array $imageFile, string $videos);
  public function findAll();
  public function findOne(int $id);
  public function updatePage(int $id); // Fusionner dans update
  public function update(Trick $trick, array $imageFiles, string $videos);
  public function delete(int $id);
  public function findAllCategories(); // Envoyer dans categoryService (findAll())
}
