<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendMailService implements SendMailServiceInterface
{
  private $mailer;

  public function __construct(MailerInterface $mailer)
  {
    $this->mailer = $mailer;
  }

  public function send(string $from, string $to, string $subject, string $template, string $token): void 
  {
    $email = (new Email())
      ->from($from)
      ->to($to)
      ->replyTo($from)
      ->subject($subject)
      ->text('Sending emails is fun again!')
      ->html('Merci de vous être inscrit sur le site Snowtricks :) <a href=http://127.0.0.1:8000/verif/' . $token . '>Cliquez ici pour activer votre compte.</a>')
    ;
    $this->mailer->send($email);
  }
}
