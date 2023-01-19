<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Service\UserServiceInterface;

class RegistrationController extends AbstractController
{
  private $user;

  public function __construct(private UserServiceInterface $userService)
  {
    $this->user = new User();
  }

  // REGISTRATION
  #[Route('/inscription', name: 'register', methods: ['GET', 'POST'])]
  public function register(Request $request): Response
  {
    $form = $this->createForm(RegistrationFormType::class, $this->user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->userService->create($this->user, $form);

      return $this->redirectToRoute('registration_succes');
    }

    return $this->render('registration/register.html.twig', [
      'registrationForm' => $form->createView(),
    ]);
  }

  // SUCCES REGISTRATION MESSAGE
  #[Route('/registration/succes', name: 'registration_succes', methods: ['GET'])]
  public function registrationSucces(): Response
  {
    return $this->render('registration/succesRegistration.html.twig');
  }

  // SUCCES ACCOUNT VALIDATION MESSAGE
  #[Route('/user/validate/{succes}', name: 'user_validate', methods: ['GET'])]
  public function validate(bool $succes): Response
  {
    return $this->render('registration/validate.html.twig', [
      'succes' => $succes
    ]);
  }

  // TOKEN VERIFICATION
  #[Route('/verif/{token}', name: 'verify_user', methods: ['GET'])]
  public function verifyUser(
    string $token,
    UserAuthenticatorInterface $userAuthenticator,
    UsersAuthenticator $authenticator,
    Request $request
  ): Response {
    $succes = false;
    $user = $this->userService->findByToken($token);

    // if token is true
    if ($user !== null) {
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
