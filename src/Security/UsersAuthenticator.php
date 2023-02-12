<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Entity\User;
use App\Service\UserServiceInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UsersAuthenticator extends AbstractLoginFormAuthenticator
{
  use TargetPathTrait;

  private $userService;

  public const LOGIN_ROUTE = 'login';

  public function __construct(private UrlGeneratorInterface $urlGenerator, UserServiceInterface $userService)
  {
    $this->userService = $userService;
  }

  public function authenticate(Request $request): Passport
  {
    $email = $request->request->get('email', '');

    $request->getSession()->set(Security::LAST_USERNAME, $email);

    $user = new User();
    $user = $this->userService->findByEmail($email);

    if($user){
      if($user->getIsVerified()){
      return new Passport(
        new UserBadge($email),
        new PasswordCredentials($request->request->get('password', '')),
        [
          new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
        ]
      );
      }
    }
    
    throw new AuthenticationException();
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
  {
    if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
      return new RedirectResponse($targetPath);
    }

    return new RedirectResponse($this->urlGenerator->generate('home'));
  }

  protected function getLoginUrl(Request $request): string
  {
    return $this->urlGenerator->generate(self::LOGIN_ROUTE);
  }
}
