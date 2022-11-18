<?php 

namespace App\Service;

interface CategoryServiceInterface
{
  public function create();
  public function findAll();
  public function getOne(int $id);
  public function updatePage(int $id);
  public function update(int $id);
  public function delete(int $id);
}
