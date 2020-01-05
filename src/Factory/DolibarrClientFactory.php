<?php


namespace App\Factory;

use Dolibarr\Client\Client;
use Dolibarr\Client\ClientBuilder;
use Dolibarr\Client\Security\Authentication\Authentication;
use Dolibarr\Client\Security\Authentication\TokenAuthentication;

/**
 * @package App\Factory
 */
final class DolibarrClientFactory
{

    /**
     * @param string $dolibarrUri
     * @param Authentication $authentication
     *
     * @return Client
     */
    public static function create(string $dolibarrUri, Authentication $authentication){
        $builder = new ClientBuilder($dolibarrUri, $authentication);
        return $builder->build();
    }
}