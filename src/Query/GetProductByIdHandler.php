<?php


namespace App\Query;

use App\Repository\Dolibarr\ProductRepository;
use App\ViewModel\Product;
use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @package App\Query
 */
final class GetProductByIdHandler
{

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param GetProductById $query
     *
     * @return Product
     */
    public function __invoke(GetProductById $query)
    {
        try {
            return $this->productRepository->getById($query->getId());
        } catch (ResourceNotFoundException $e) {
            throw new NotFoundHttpException();
        } catch (ApiException $e) {
            throw new HttpException(500);
        }
    }
}
