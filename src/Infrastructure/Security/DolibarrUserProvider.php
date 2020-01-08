<?php


namespace App\Infrastructure\Security;

use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Exception\ResourceNotFoundException;
use Dolibarr\Client\Service\LoginService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * User provider for Dolibarr.
 *
 * @package App\Infrastructure\Security
 */
final class DolibarrUserProvider implements UserProviderInterface
{

    /**
     * @var LoginService
     */
    private $loginService;

    /**
     * DolibarrUserProvider constructor.
     *
     * @param LoginService $loginService
     */
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername(string $username)
    {
        throw new UsernameNotFoundException('Not supported');
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException  if the user is not supported
     * @throws UsernameNotFoundException if the user is not found
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass(string $class)
    {
        return User::class === $class;
    }
}
