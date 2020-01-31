<?php


namespace App\Infrastructure\Security;

use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Service\LoginService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * @package App\Infrastructure\Security
 */
final class DolibarrAuthenticator extends AbstractFormLoginAuthenticator
{

    /**
     * @var LoginService
     */
    private $loginService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    public function __construct(LoginService $loginService, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->loginService = $loginService;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return 'login' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        return [
            'login'      => $request->request->get('username'),
            'password'   => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $login = $credentials['login'];
        $password = $credentials['password'];

        $this->validateCsrf(new CsrfToken('authenticate', $credentials['csrf_token']));

        try {
            $this->loginService->login($login, $password);

            return new User($login, $password);
        } catch (ApiException $e) {
            throw new CustomUserMessageAuthenticationException('Erreur de login');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->router->generate('index'));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * Return the URL to the login page.
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->router->generate('login_screen');
    }

    private function validateCsrf(CsrfToken $token): void
    {
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
    }
}
