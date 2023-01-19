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

  // UPDATE
  #[Route('/user/update', name: 'user_update', methods: ['GET', 'POST'])]
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

  // DELETE SEND MAIL TOKEN REQUEST
  #[Route('/user/delete/request', name: 'user_delete_request', methods: 'GET')]
  public function deleteRequest(): Response
  {
    if($this->getUser()){
      $this->userService->sendAccountDeletionToken($this->getUser());

      $this->addFlash('succes', 'Un email pour supprimer votre compte vous a été envoyé.');
      return $this->redirectToRoute('home');
    }

    $this->addFlash('error', 'Une erreur est survenue.');
    return $this->redirectToRoute('home');
  }

  #[Route('/user/delete/validate/{token}', name: 'user_delete_validate', methods: 'GET')]
  public function delete(string $token): Response
  {
    $accountDeleted = false;
    $user = $this->userService->findByAccountDeletionToken($token);
    if($user !== null){
      $this->userService->delete($user);
      $accountDeleted = true;
    }
    if($accountDeleted){
      $this->addFlash('succes', 'Votre compte a bien été supprimé. Toutes vos données personnelles seront effacées dans un délai de 48h.');
    } else {
      $this->addFlash('error', 'Lien invalide.');
    }
    
    return $this->redirectToRoute('home');
  }
}
