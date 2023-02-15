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
      ->html('Merci de vous être inscrit sur le site Snowtricks :) <a href=https://valentin-moreau.com/verif/' . $token . '>Cliquez ici pour activer votre compte.</a>')
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
      ->html('Vous avez demandé un lien de modification de mot de passe. Si vous souhaitez changer votre mot de passe, <a href=https://valentin-moreau.com/password/recovery/new/' . $token . '>Cliquez ici.</a>')
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
      ->html('Vous avez demandé de supprimer votre compte Snowtricks. Si vous souhaitez réellement supprimer votre compte, <a href=https://valentin-moreau.com/user/delete/validate/' . $token . '>Cliquez ici.</a>')
    ;
    $this->mailer->send($email);
  }
}
