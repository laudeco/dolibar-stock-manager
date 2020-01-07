<?php


namespace App\Repository\Dolibarr;

use App\Factory\DolibarrClientFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Dolibarr\Client\Client;
use Dolibarr\Client\Domain\Common\Barcode;
use Dolibarr\Client\Domain\Product\Product;
use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Exception\ResourceNotFoundException;
use Dolibarr\Client\Security\Authentication\LoginAuthentication;
use Dolibarr\Client\Service\ProductsService;
use Symfony\Component\Security\Core\Security;

/**
 * @package App\Repository
 */
final class ProductRepository
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
     * @var ProductsService
     */
    private $productService;

    /**
     * @param DolibarrClientFactory $factory
     * @param Security $security
     */
    public function __construct(DolibarrClientFactory $factory, Security $security)
    {
        $this->factory = $factory;
        $this->security = $security;
    }

    /**
     * @return Client
     */
    private function client(){
        $user = $this->security->getUser();
        if($user){
            $this->factory->setAuthentication(new LoginAuthentication($user->getUsername(), $user->getPassword()));
        }

        return $this->factory->create();
    }

    /**
     * @return ProductsService
     */
    private function service(){
        if(null !== $this->productService){
            return $this->productService;
        }

        return $this->client()->products();
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return ArrayCollection|Product[]
     *
     * @throws ApiException
     */
    public function findAll(int $page = 0, int $limit = 100)
    {
        try{
            return $this->service()->getAll($page, $limit);
        }catch(ResourceNotFoundException $exception){
            return new ArrayCollection();
        }
    }

    /**
     * @param string $barcode
     *
     * @return ArrayCollection|Product[]
     *
     * @throws ApiException
     */
    public function getByBarcode(string $barcode){
        return $this->service()->getByBarcode(new Barcode($barcode));
    }
}