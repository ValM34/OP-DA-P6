<?php 

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;

interface CategoryServiceInterface
{
  public function findAll();
  public function findOne(int $id);
  public function create(User $user, Category $category): void;
  public function updatePage(int $id);
  public function update(int $id);
  public function delete(int $id): void;
}
