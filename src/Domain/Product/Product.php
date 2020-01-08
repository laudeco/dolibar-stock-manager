<?php


namespace App\Domain\Product;

final class Product
{
    /**
     * @var ProductId
     */
    private $product;

    /**
     * @var Counter
     */
    private $modification;

    /**
     * @param ProductId $id
     * @param Counter   $modification
     */
    public function __construct(ProductId $id, Counter $modification)
    {
        $this->product = $id;
        $this->modification = $modification;
    }

    /**
     * @param int $numberOfModifications
     *
     * @return Product
     */
    public function applyModifications(int $numberOfModifications)
    {
        return new $this($this->product, $this->modification->apply($numberOfModifications));
    }

    /**
     * @return ProductId
     */
    public function getId()
    {
        return $this->product;
    }

    /**
     * @return Counter
     */
    public function getCounter()
    {
        return $this->modification;
    }
}
