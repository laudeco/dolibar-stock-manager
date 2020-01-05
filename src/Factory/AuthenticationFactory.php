<?php


namespace App\Factory;

use Dolibarr\Client\Security\Authentication\Authentication;
use Dolibarr\Client\Security\Authentication\TokenAuthentication;

/**
 * @package App\Factory
 */
final class AuthenticationFactory
{

    /**
     * @param string $token
     *
     * @return Authentication
     */
    public static function create($token){
        return new TokenAuthentication($token);
    }

}