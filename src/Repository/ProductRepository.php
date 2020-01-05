<?php


namespace App\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dolibarr\Client\Domain\Product\Product;
use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Exception\ResourceNotFoundException;
use Dolibarr\Client\Service\ProductsService;

/**
 * @package App\Repository
 */
final class ProductRepository
{

    /**
     * @var ProductsService
     */
    private $productService;

    /**
     * ProductRepository constructor.
     * @param ProductsService $productService
     */
    public function __construct(ProductsService $productService)
    {
        $this->productService = $productService;
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
            return $this->productService->getAll($page, $limit);
        }catch(ResourceNotFoundException $exception){
            return new ArrayCollection();
        }
    }
}