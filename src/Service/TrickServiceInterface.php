<?php 

namespace App\Service;

interface TrickServiceInterface
{
  public function create();
  public function findAll();
  public function getOne(int $id);
  public function updatePage(int $id);
  public function update(int $id);
  public function delete(int $id);
  public function getMessages(int $id);
  public function findAllCategories();
  public function createMessage($id);
}
