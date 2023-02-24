<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\TrickRepository;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\UserServiceInterface;

class MessageApiController extends AbstractController
{
  public function __construct(
    private SerializerInterface $serializer,
    private UserServiceInterface $userService
  )
  {}

  // GET MESSAGES BY TRICK
  #[Route('/api/message/{slug}', requirements: ['slug' => '[a-zA-Z0-9\-]+'], name: 'app_display_messages', methods: ['GET'])]
  public function getCustomers(string $slug, TrickRepository $trickRepository, Request $request): JsonResponse
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 5);
    $context = (new ObjectNormalizerContextBuilder())
      ->withGroups('get_messages')
      ->toArray()
    ;
    $customerList = $trickRepository->getMessages($slug, $page, $limit);
    $jsonCustomerList = $this->serializer->serialize($customerList, 'json', $context);
    
    if($this->getUser() !== null){
      $actualUser = $this->userService->findOne($this->getUser())->getId();
      $actualUserJson = ['actualUser' => $actualUser];
      $jsonCustomerList = json_encode(
        array_merge(
          json_decode($jsonCustomerList, true),
          $actualUserJson
        )
      );
    }
    
    if($customerList === null){
      
      return new JsonResponse('{"res": "null"}', Response::HTTP_OK, [], true);
    }

    return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
  }
}
