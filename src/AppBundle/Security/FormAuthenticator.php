<?php


namespace AppBundle\Security;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class FormAuthenticator extends AbstractFormLoginAuthenticator
{
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;
    /** @var RouterInterface */
    private $router;
    /**
     * SecurityAuthenticator constructor.
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param RouterInterface $router
     */
    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        RouterInterface $router
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->router = $router;
    }
    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'login_check'
            && $request->isMethod('POST');
    }
    /**
     * @param Request $request
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        // Array with $form->getData()
        $credentials = [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        // Define LAST_USERNAME
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );
        return $credentials;
    }
    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('passphrase', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException('Token invalide');
        }
        // Check if user exist
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            [
                'username' => $credentials['username'],
            ]
        );
        if (\is_null($user)) {
            throw new CustomUserMessageAuthenticationException('Mauvais identifiant.');
        }
        return $user;
    }
    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // Check if password is valid
        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            throw new CustomUserMessageAuthenticationException('Mot de passe incorrect.');
        }
        return true;
    }
    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // Success => Redirection a la page d'accueil
        return new RedirectResponse($this->router->generate('homepage'));
    }
    /**
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->router->generate('login');
    }
}
