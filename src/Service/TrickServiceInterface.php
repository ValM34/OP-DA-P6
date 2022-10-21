<?php 

namespace App\Service;

interface TrickServiceInterface
{
  public function create();
  public function getAll();
  public function getOne(int $id);
  public function updatePage(int $id);
  public function update(int $id);
  public function delete(int $id);
}
