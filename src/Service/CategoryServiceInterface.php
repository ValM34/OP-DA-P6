<?php 

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;

interface CategoryServiceInterface
{
  public function findAll(): array;
  public function create(User $user, Category $category): void;
  public function update(Category $category): void;
  public function delete($category): void;
}
