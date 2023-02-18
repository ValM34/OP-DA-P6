<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\User;
use App\Service\ImageServiceInterface;
use App\Service\VideoServiceInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Form;

class TrickService implements TrickServiceInterface
{
  public function __construct(
    protected SluggerInterface $slugger,
    private EntityManagerInterface $entityManager,
    private ImageServiceInterface $imageService,
    private VideoServiceInterface $videoService
  )
  {}

  // FIND ALL
  public function findAll(): array
  {
    $tricks = $this->entityManager->getRepository(Trick::class)->findAllTricks();
    for ($i = 0, $count = count($tricks); $i < $count; $i++) {
      if ($tricks[$i]['path'] !== null) {
        $tricks[$i]['path'] = 'images/tricks/' . $tricks[$i]['path'];
      } else {
        $tricks[$i]['path'] = 'images/trickDefault.jpg';
      }
    }

    return $tricks;
  }

  // FIND ONE
  public function findOne(int $id): array
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);
    if (!$trick) {
      $trick = null;
    }

    if($trick !== null){
      $images = [];
      $imagePath = null;
      
      if ($trick->getImages()[0] !== null) {
        $imagePath = $trick->getImages()[0]->getPath();
        for ($i = 0, $count = count($trick->getImages()); $i < $count; $i++) {
          $images[$i]['path'] = $trick->getImages()[$i]->getPath();
          $images[$i]['id'] = $trick->getImages()[$i]->getId();
        }
      } else {
        $imagePath = '../trickDefault.jpg';
      }

      $videos = [];
      if ($trick->getVideos()[0] !== null) {
        for ($i = 0, $count = count($trick->getVideos()); $i < $count; $i++) {
          $videos[$i]['path'] = $trick->getVideos()[$i]->getPath();
          $videos[$i]['id'] = $trick->getVideos()[$i]->getId();
        }
      }
    }

    return [$trick, $imagePath, $images, $videos];
  }

  // CREATE
  public function create(User $user, Trick $trick, Form $form): Trick
  {
    $date = new DateTimeImmutable();
    $imageFiles = $form->get('image')->getData();
    $videos = $form->get('videos')->getData();
    if ($videos === null) {
      $videos = '';
    }

    $trick
      ->setUser($user)
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
      ->setSlug(strtolower($this->slugger->slug($trick->getName()))) //@TODO strtolower pour les autres slug
    ;
    
    $this->entityManager->persist($trick);
    $this->imagesProcessing($trick, $imageFiles);
    $videosArray = $this->filterVideos($videos);
    $this->videoService->create($trick, $videosArray);

    $this->entityManager->flush();

    return $trick;
  }

  // UPDATE
  public function update(Trick $trick, Form $form): void
  {
    $date = new DateTimeImmutable();
    $imageFiles = $form->get('image')->getData();
    $videos = $form->get('videos')->getData();
    if ($videos === null) {
      $videos = '';
    }
    $trick
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
      ->setSlug(strtolower($this->slugger->slug($trick->getName())))
    ;
    $this->entityManager->persist($trick);
    $this->imagesProcessing($trick, $imageFiles);
    $videosArray = $this->filterVideos($videos);
    $this->videoService->create($trick, $videosArray);

    $this->entityManager->flush();
  }

  // DELETE
  public function delete(Trick $trick): void
  {
    $images = $this->entityManager->getRepository(Image::class)->findByTrick($trick);
    $this->imageService->deleteByTrick($images);
    $this->entityManager->remove($trick);
    $this->entityManager->flush();
  }

  // FILTER VIDEOS
  public function filterVideos(string $videos): array
  {
    $videos = str_replace(' ', '', $videos);
    $separators = ", ;";
    $videosArray = preg_split("/[" . $separators . "]/", $videos);
    foreach($videosArray as $video){
      if (str_starts_with($video, 'https://www.youtube.com') !== false || str_starts_with($video, 'https://vimeo.com') !== false) {
        // La chaîne contient l'URL de YouTube ou l'URL de Vimeo
      } else {
        // La chaîne ne contient pas l'URL de YouTube ni l'URL de Vimeo
        $videosArray = array_diff($videosArray, [$video]);
      }
    }
    $videosArray = array_values($videosArray);
    $videosArray = str_replace('https://www.youtube.com/watch?v=', 'https://www.youtube.com/embed/', $videosArray);
    $videosArray = str_replace('https://vimeo.com/', 'https://player.vimeo.com/video/', $videosArray);
    
    return $videosArray;
  }

  // IMAGES PROCESSING
  public function imagesProcessing(Trick $trick, array $imageFiles): void
  {
    if (isset($imageFiles[0])) {
      $arrayOfImages = [];
      foreach($imageFiles as $imageFile){
        $arrayOfImages[] = $this->imageService->upload($imageFile);
      }
      $this->imageService->create($trick, $arrayOfImages);
    }
  }
}
