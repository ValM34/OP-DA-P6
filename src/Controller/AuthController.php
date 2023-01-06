<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\PasswordRecoveryForm;
use App\Form\PasswordUpdateForm;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
  private $userService;

  public function __construct(UserServiceInterface $userService)
  {
    $this->userService = $userService;
  }

  // LOGIN
  #[Route(path: '/login', name: 'login')]
  public function login(AuthenticationUtils $authenticationUtils): Response
  {
    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();
    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
  }

  // LOGOUT
  #[Route(path: '/logout', name: 'logout')]
  public function logout(): void
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }

  // SEND PASSWORD RECOVERY
  #[Route(path: '/password/recovery/send', name: 'send_password_recovery')]
  public function sendTokenRecovery(Request $request)
  {
    $user = new User();
    $form = $this->createForm(PasswordRecoveryForm::class, $user);
    $form->handleRequest($request);

    if($form->isSubmitted()){
      $user = $this->userService->findByEmail($user->getEmail());
      $this->userService->sendPasswordRecoveryToken($user);

      return $this->render('security/passwordRecoveryRequested.html.twig');
    }

    return $this->render('security/passwordRecoveryForm.html.twig', [
      'recoveryForm' => $form->createView()
    ]);
  }

  // PASSWORD RECOVERY
  #[Route(path: '/password/recovery/new/{token}', name: 'password_recovery')]
  public function passwordRecovery($token, Request $request, UserPasswordHasherInterface $userPasswordHasher)
  {
    $user = $this->userService->findByRecoveryToken($token);
    if($user){
      $form = $this->createForm(PasswordUpdateForm::class, $user);
      $form->handleRequest($request);

      if($form->isSubmitted()){
        // encode the plain password
        $user->setPassword(
          $userPasswordHasher->hashPassword(
            $user,
            $form->get('plainPassword')->getData()
          )
        );
        $user->setPasswordRecoveryToken('used');
        $this->userService->update($user);

        return $this->redirectToRoute('login');
      }

      return $this->render('security/passwordUpdateForm.html.twig', [
        'updateForm' => $form->createView()
      ]);
    }

    return $this->redirectToRoute('home');
  }
}
