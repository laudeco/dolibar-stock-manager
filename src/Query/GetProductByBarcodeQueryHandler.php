<?php


namespace App\Query;

use App\Repository\Dolibarr\ProductRepository;
use App\ViewModel\Product;
use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @package App\Query
 */
final class GetProductByBarcodeQueryHandler
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
     * @param GetProductByBarcodeQuery $query
     *
     * @return Product
     *
     * @throws ApiException
     */
    public function __invoke(GetProductByBarcodeQuery $query)
    {
        try {
            $products = $this->productRepository->getByBarcode($query->barcode());
            /** @var \Dolibarr\Client\Domain\Product\Product $currentProduct */
            $currentProduct = $products->first();

            $apiProduct = new Product();
            $apiProduct->setLabel($currentProduct->getLabel());
            $apiProduct->setCodebar($currentProduct->getBarcode());
            $apiProduct->setId(intval($currentProduct->getId(), 10));

            return $apiProduct;
        } catch (ResourceNotFoundException $e) {
            throw new NotFoundHttpException();
        }
    }
}
