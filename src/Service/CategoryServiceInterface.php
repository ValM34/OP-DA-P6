<?php 

namespace App\Service;

use App\Entity\Category;

interface CategoryServiceInterface
{
  public function create($user, Category $category);
  public function findAll();
  public function findOne(int $id);
  public function updatePage(int $id);
  public function update(int $id);
  public function delete(int $id);
}
