<?php 

namespace App\Service;

use App\Entity\Trick;

interface TrickServiceInterface
{
  public function findAll();
  public function findOne(int $id);
  public function create($user, Trick $trick, array $imageFile, string $videos): void;
  public function updatePage(int $id); // Fusionner dans update
  public function update(Trick $trick, array $imageFiles, string $videos): void;
  public function delete(int $id): void;
  public function findAllCategories(); // Envoyer dans categoryService (findAll())
}
