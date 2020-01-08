<?php


namespace App\Repository;

use App\Domain\Product\Counter;
use App\Domain\Product\Product;
use App\Domain\Product\ProductId;

final class ProductRepository
{
    /**
     * @var DbManager
     */
    private $dbManager;

    /**
     * @param DbManager $dbManager
     */
    public function __construct(DbManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    /**
     * @param Product $product
     */
    public function save(Product $product)
    {
        $this->dbManager->save($this->fromEntity($product));
    }

    /**
     * @param string $id
     *
     * @return Product
     */
    public function getById(string $id)
    {
        $product = $this->dbManager->getById($id);

        return $this->toEntity($product);
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function fromEntity(Product $product)
    {
        return [
            'id'      => $product->getId()->getId(),
            'counter' => $product->getCounter()->getValue(),
        ];
    }

    /**
     * @param array $product
     *
     * @return Product
     */
    private function toEntity(array $product)
    {
        return new Product(new ProductId($product['id']), new Counter($product['counter']));
    }
}
