<?php 

namespace App\Service;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Form\Form;

interface TrickServiceInterface
{
  public function findAll(): array;
  public function findOne(int $id): array;
  public function create(User $user, Trick $trick, Form $form): Trick;
  public function update(Trick $trick, Form $form): void;
  public function delete(Trick $trick): void;
  public function filterVideos(string $videos): array;
  public function imagesProcessing(Trick $trick, array $imageFiles): void;
}
