<?php


namespace App\Repository\Dolibarr;

use App\Factory\DolibarrClientFactory;
use Dolibarr\Client\Client;
use Dolibarr\Client\Security\Authentication\LoginAuthentication;
use Dolibarr\Client\Service\AbstractService;
use Symfony\Component\Security\Core\Security;

abstract class DolibarrRepository
{
    /**
     * @var DolibarrClientFactory
     */
    private $factory;

    /**
     * @var Security
     */
    private $security;

    /**
     * @param DolibarrClientFactory $factory
     * @param Security              $security
     */
    public function __construct(DolibarrClientFactory $factory, Security $security)
    {
        $this->factory = $factory;
        $this->security = $security;
    }

    /**
     * @return Client
     */
    protected function client()
    {
        $user = $this->security->getUser();
        if ($user) {
            $this->factory->setAuthentication(new LoginAuthentication($user->getUsername(), $user->getPassword()));
        }

        return $this->factory->create();
    }

    /**
     * Return the service needed for this repository.
     *
     * @return AbstractService
     */
    abstract protected function service();
}
