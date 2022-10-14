<?php 

namespace App\Service;

interface TrickServiceInterface
{
  public function create();
  public function readAll();
  public function update(int $id);
}
