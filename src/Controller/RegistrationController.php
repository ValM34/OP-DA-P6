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
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\UserServiceInterface;

class RegistrationController extends AbstractController
{
  private $userService;

  public function __construct(UserServiceInterface $userService)
  {
    $this->userService = $userService;
  }

  // SIGN UP
  #[Route('/inscription', name: 'register')]
  public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
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

      $avatar = $form->get('avatar')->getData();
      if($avatar !== null){
        $avatarPath = $this->userService->upload($avatar);
        $this->userService->create($user, $avatarPath);
      } else {
        $this->userService->create($user);
      }
      

      // do anything else you need here, like send an email @TODO envoi d'email de validation

      return $userAuthenticator->authenticateUser(
        $user,
        $authenticator,
        $request
      );
    }

    return $this->render('registration/register.html.twig', [
      'registrationForm' => $form->createView(),
    ]);
  }
}
