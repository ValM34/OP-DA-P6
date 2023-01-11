<?php 

namespace App\Service;

use App\Entity\Trick;
use App\Entity\User;

interface TrickServiceInterface
{
  public function findAll(): array;
  public function findOne(int $id): Trick;
  public function create(User $user, Trick $trick, array $imageFile, string $videos): void;
  public function updatePage(int $id): Trick;
  public function update(Trick $trick, array $imageFiles, string $videos): void;
  public function delete(int $id): void;
  public function filterVideos(string $videos): array;
}
