<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\User;
use Twig\Environment;

class SendMailService implements SendMailServiceInterface
{
  public function __construct(
    private MailerInterface $mailer,
    private Environment $twig
  )
  {}

  // SEND EMAIL VALIDATION
  public function emailValidation(string $from, string $to, string $subject, string $token): void 
  {
    $email = (new Email())
      ->from($from)
      ->to($to)
      ->replyTo($from)
      ->subject($subject)
      ->html($this->twig->render('email/emailValidation.html.twig', ['token' => $token]))
    ;
    $this->mailer->send($email);
  }

  // SEND PASSWORD RECOVERY EMAIL
  public function passwordRecovery(User $user, string $token): void 
  {
    $email = (new Email())
      ->from('contact@valentin-moreau.com')
      ->to($user->getEmail())
      ->replyTo('contact@valentin-moreau.com')
      ->subject('Modification du mot de passe')
      ->html($this->twig->render('email/passwordRecovery.html.twig', ['token' => $token]))
    ;
    $this->mailer->send($email);
  }

  // ACCOUNT DELETION
  public function accountDeletion(User $user, string $token): void
  {
    $email = (new Email())
      ->from('contact@valentin-moreau.com')
      ->to($user->getEmail())
      ->replyTo('contact@valentin-moreau.com')
      ->subject('Suppression du compte Snowtricks')
      ->html($this->twig->render('email/accountDeletion.html.twig', ['token' => $token]))
    ;
    $this->mailer->send($email);
  }
}
