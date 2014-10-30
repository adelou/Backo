<?php

namespace App\UserBundle\Security;


use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    protected $router;
    protected $security;

    public function __construct(Router $router, SecurityContext $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $current_url = $request->server->get('HTTP_ORIGIN').$this->router->generate('fos_user_security_login');
        $referer_url = $request->headers->get('referer');

        if ($this->security->isGranted('ROLE_SUPER_ADMIN'))
        {
            $response = new RedirectResponse($this->router->generate('user_list'));
        }
        elseif ($token->getUser()->getFirstconnexion() != 1)
        {
            $response = new RedirectResponse($this->router->generate('fos_user_change_password'));
        }
        elseif($current_url == $referer_url)
        {
            $response = new RedirectResponse($this->router->generate('dashboard'));
        }
        else {
            $response = new RedirectResponse($referer_url);
        }

        return $response;
    }

}