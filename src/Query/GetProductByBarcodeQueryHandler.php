<?php


namespace App\Query;

use App\Exception\ProductNotFoundException;
use App\Repository\Dolibarr\ProductRepository;
use App\ViewModel\Product;
use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Exception\ResourceNotFoundException;

/**
 * @package App\Query
 */
final class GetProductByBarcodeQueryHandler implements QueryHandlerInterface
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
     * @throws ProductNotFoundException
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
            $apiProduct->setBarcode($currentProduct->getBarcode());
            $apiProduct->setId(intval($currentProduct->getId(), 10));
            $apiProduct->setSerialNumberable($currentProduct->isBatchUsage());

            return $apiProduct;
        } catch (ResourceNotFoundException $e) {
            throw new ProductNotFoundException();
        }
    }

    /**
     * @param GetProductByBarcodeQuery $query
     *
     * @return Product
     *
     * @throws ProductNotFoundException
     * @throws ApiException
     */
    public function handle($query)
    {
        return $this->__invoke($query);
    }
}
