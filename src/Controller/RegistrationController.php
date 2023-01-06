<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Service\UserServiceInterface;
use App\Service\SendMailServiceInterface;
use App\Repository\UserRepository;

class RegistrationController extends AbstractController
{
  private $userService;

  public function __construct(UserServiceInterface $userService)
  {
    $this->userService = $userService;
  }

  // REGISTRATION
  #[Route('/inscription', name: 'register')]
  public function register(
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    SendMailServiceInterface $mail
  ): Response 
  {
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // encode the plain password
      $user->setPassword(
        $userPasswordHasher->hashPassword(
          $user,
          $form->get('plainPassword')->getData()
        )
      );

      $token = hash('sha512', $user->getEmail() . uniqId() . 'dsf51dsf15dsSDFSf521d65s');
      $user->setRegistrationToken($token);

      $avatar = $form->get('avatar')->getData();
      if ($avatar !== null) {
        $avatarPath = $this->userService->upload($avatar);
        $this->userService->create($user, $avatarPath);
      } else {
        $this->userService->create($user);
      }

      // Send validation mail
      $mail->emailValidation(
        'snow@tricks.fr',
        $user->getEmail(),
        'Activation de votre compte sur le site Snowtricks',
        $token
      );

      return $this->redirectToRoute('registration_succes');
    }

    return $this->render('registration/register.html.twig', [
      'registrationForm' => $form->createView(),
    ]);
  }

  // SUCCES REGISTRATION MESSAGE
  #[Route('/registration/succes', name: 'registration_succes')]
  public function registrationSucces()
  {
    return $this->render('registration/succesRegistration.html.twig');
  }

  // SUCCES ACCOUNT VALIDATION MESSAGE
  #[Route('/user/validate/{succes}', name: 'user_validate')]
  public function validate(bool $succes)
  {
    return $this->render('registration/validate.html.twig', [
      'succes' => $succes
    ]);
  }

  // TOKEN VERIFICATION
  #[Route('/verif/{token}', name: 'verify_user')]
  public function verifyUser($token, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, Request $request): Response
  {
    $user = $this->userService->findByToken($token);

    // if token is true
    if($user){
      $this->userService->validateUser($user);
      $userAuthenticator->authenticateUser(
        $user,
        $authenticator,
        $request
      );
      $succes = true;
      
      return $this->redirectToRoute('user_validate', ['succes' => $succes]);
    }

    // if token is false
    $succes = 0;

    return $this->redirectToRoute('user_validate', ['succes' => $succes]);
  }
}
