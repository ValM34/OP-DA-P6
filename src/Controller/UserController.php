<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UpdateUserForm;
use App\Security\UsersAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Service\UserServiceInterface;
use App\Service\SendMailServiceInterface;

class UserController extends AbstractController
{
  private $user;

  public function __construct(private UserServiceInterface $userService, private SendMailServiceInterface $sendMailService)
  {
    $this->user = new User();
  }

  #[Route('/user/update', name: 'user_update')]
  public function update(Request $request): Response
  {
    if ($this->getUser() === null) {
      return $this->redirectToRoute('login');
    } else {
      $form = $this->createForm(UpdateUserForm::class, $this->user);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $user = $this->userService->findOne($this->getUser());
        if($this->user->getFirstName() !== null){
          $user->setFirstName($this->user->getFirstName());
        }
        if($this->user->getLastName() !== null){
          $user->setLastName($this->user->getLastName());
        }
        if($this->user->getAvatar() !== null){
          $user->setAvatar($this->user->getAvatar());
        }
        $this->userService->update($user, $form);
        
        return $this->redirectToRoute('home');
      }

      return $this->render('user/update.html.twig', [
        'form' => $form->createView(),
      ]);
    }
  }

  #[Route('/user/delete/request', name: 'user_delete_request')]
  public function deleteRequest()
  {
    if($this->getUser()){
      $this->sendMailService->accountDeletion($this->getUser());

      return $this->redirectToRoute('user_update');
    }

    return $this->redirectToRoute('home');
  }

  #[Route('/user/delete/validate/{id}', name: 'user_delete_validate')]
  public function delete(int $id)
  {
    if($this->getUser()){
      
      return $this->redirectToRoute('home');
    }

    return $this->redirectToRoute('home');
  }
}
