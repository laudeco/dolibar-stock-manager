<?php


namespace App\Factory;

use Dolibarr\Client\Security\Authentication\Authentication;
use Dolibarr\Client\Security\Authentication\LoginAuthentication;
use Dolibarr\Client\Security\Authentication\NoAuthentication;
use Symfony\Component\Security\Core\Security;

/**
 * @package App\Factory
 */
final class AuthenticationFactory
{

    /**
     * @var Security
     */
    private $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    /**
     * @return Authentication
     */
    public function create(){
        $user = $this->security->getUser();
        if(!$user){
            return new NoAuthentication();
        }

        $login = $user->getUsername();
        $password = $user->getPassword();

        return new LoginAuthentication($login, $password);
    }

}