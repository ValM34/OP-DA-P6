<?php 

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;

interface CategoryServiceInterface
{
  public function findAll(): array;
  public function findOne(int $id): Category;
  public function create(User $user, Category $category): void;
  public function updatePage(int $id): Category;
  public function update(int $id): void;
  public function delete(int $id): void;
}
