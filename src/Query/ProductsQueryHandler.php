<?php


namespace App\Query;

use App\Repository\ProductRepository;
use App\ViewModel\Product;
use Dolibarr\Client\Exception\ApiException;

/**
 * @package App\Query
 */
final class ProductsQueryHandler
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
     * @param ProductQuery $query
     *
     * @return array|Product[]
     *
     * @throws ApiException
     */
    public function __invoke(ProductQuery $query){
        $results = $this->productRepository->findAll($query->getPage(), $query->getLimit());

        $products =[];
        foreach($results as $currentProduct){
            $productViewModel = new Product();

            $productViewModel->setLabel($currentProduct->getLabel());
            if($currentProduct->getBarcode()){
                $productViewModel->setCodebar($currentProduct->getBarcode());
            }

            $products[] = $productViewModel;
        }

        return $products;
    }
}