<?php


namespace App\Factory;

use Dolibarr\Client\Client;
use Dolibarr\Client\ClientBuilder;
use Dolibarr\Client\Security\Authentication\Authentication;
use Dolibarr\Client\Security\Authentication\NoAuthentication;

/**
 * @package App\Factory
 */
final class DolibarrClientFactory
{

    /**
     * @var string
     */
    private $dolibarrUri;

    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @param string $dolibarrUri
     */
    public function __construct(string $dolibarrUri)
    {
        $this->dolibarrUri = $dolibarrUri;
        $this->authentication = new NoAuthentication();
    }

    /**
     * @param Authentication $authentication
     */
    public function setAuthentication(Authentication $authentication): void
    {
        $this->authentication = $authentication;
    }

    /**
     * @return Client
     */
    public function create(){
        $builder = new ClientBuilder($this->dolibarrUri, $this->authentication);
        $builder->setDebug(false);
        return $builder->build();
    }
}