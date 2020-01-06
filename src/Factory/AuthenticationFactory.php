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
     * @return Authentication
     */
    public static function create(){
        return new TokenAuthentication('');
    }

}