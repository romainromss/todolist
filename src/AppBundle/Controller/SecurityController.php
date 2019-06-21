<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;


class SecurityController
{
    /** @var Environment */
    protected $twig;
    public function __construct(
        Environment $twig
    ) {
        $this->twig = $twig;
    }
    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return new Response(
            $this->twig->render(
                'security/login.html.twig',
                [
                    'last_username' => $lastUsername,
                    'error'         => $error,
                ]
            )
        );
    }
}
