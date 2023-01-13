<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Service\UserServiceInterface;
use App\Service\SendMailServiceInterface;

class RegistrationController extends AbstractController
{
  private $userService;

  public function __construct(UserServiceInterface $userService)
  {
    $this->userService = $userService;
  }

  // REGISTRATION
  #[Route('/inscription', name: 'register')]
  public function register(Request $request): Response 
  {
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) { 
      $this->userService->create($user, $form);

      return $this->redirectToRoute('registration_succes');
    }

    return $this->render('registration/register.html.twig', [
      'registrationForm' => $form->createView(),
    ]);
  }

  // SUCCES REGISTRATION MESSAGE
  #[Route('/registration/succes', name: 'registration_succes')]
  public function registrationSucces(): Response
  {
    return $this->render('registration/succesRegistration.html.twig');
  }

  // SUCCES ACCOUNT VALIDATION MESSAGE
  #[Route('/user/validate/{succes}', name: 'user_validate')]
  public function validate(bool $succes): Response
  {
    return $this->render('registration/validate.html.twig', [
      'succes' => $succes
    ]);
  }

  // TOKEN VERIFICATION
  #[Route('/verif/{token}', name: 'verify_user')]
  public function verifyUser(string $token, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, Request $request): Response
  {
    $succes = false;
    $user = $this->userService->findByToken($token);

    // if token is true
    if($user !== null){
      $this->userService->validateUser($user);
      $userAuthenticator->authenticateUser(
        $user,
        $authenticator,
        $request
      );
      $succes = true;
    }

    return $this->redirectToRoute('user_validate', ['succes' => $succes]);
  }
}
