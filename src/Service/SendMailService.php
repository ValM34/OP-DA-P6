<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\User;

class SendMailService implements SendMailServiceInterface
{
  private $mailer;

  public function __construct(MailerInterface $mailer)
  {
    $this->mailer = $mailer;
  }

  // SEND EMAIL VALIDATION
  public function emailValidation(string $from, string $to, string $subject, string $token): void 
  {
    $email = (new Email())
      ->from($from)
      ->to($to)
      ->replyTo($from)
      ->subject($subject)
      ->html('Merci de vous être inscrit sur le site Snowtricks :) <a href=http://127.0.0.1:8000/verif/' . $token . '>Cliquez ici pour activer votre compte.</a>')
    ;
    $this->mailer->send($email);
  }

  // SEND PASSWORD RECOVERY EMAIL
  public function passwordRecovery(User $user, string $token): void 
  {
    $email = (new Email())
      ->from('snow@tricks.fr')
      ->to($user->getEmail())
      ->replyTo('snow@tricks.fr')
      ->subject('Récupération du mot de passe')
      ->html('Vous avez demandé un lien de récupération de mot de passe. Pour en créer un nouveau, <a href=http://127.0.0.1:8000/password/recovery/new/' . $token . '>Cliquez ici.</a>')
    ;
    $this->mailer->send($email);
  }
}
