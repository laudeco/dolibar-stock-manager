<?php


namespace App\Repository\Dolibarr;

use Doctrine\Common\Collections\ArrayCollection;
use Dolibarr\Client\Domain\Common\Barcode;
use Dolibarr\Client\Domain\Product\Product;
use Dolibarr\Client\Domain\Product\ProductId;
use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Exception\ResourceNotFoundException;
use Dolibarr\Client\Service\ProductsService;

/**
 * @package App\Repository
 */
final class ProductRepository extends DolibarrRepository
{
    /**
     * @var ProductsService
     */
    private $productService;

    /**
     * @return ProductsService
     */
    protected function service()
    {
        if (null !== $this->productService) {
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
        try {
            return $this->service()->getAll($page, $limit);
        } catch (ResourceNotFoundException $exception) {
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
    public function getByBarcode(string $barcode)
    {
        return $this->service()->getByBarcode(new Barcode($barcode));
    }

    /**
     * @param int $id
     *
     * @return \App\ViewModel\Product
     *
     * @throws ApiException
     */
    public function getById(int $id)
    {
        $product = $this->service()->getById(new ProductId($id));

        $viewProduct = new \App\ViewModel\Product();

        $viewProduct->setLabel($product->getLabel());
        $viewProduct->setCodebar($product->getBarcode());
        $viewProduct->setId($product->getId());

        return $viewProduct;
    }
}
