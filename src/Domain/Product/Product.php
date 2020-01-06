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
     * @param ProductId $product
     * @param Counter $modification
     */
    public function __construct(ProductId $product, Counter $modification)
    {
        $this->product = $product;
        $this->modification = $modification;
    }

    /**
     * @param int $numberOfModifications
     *
     * @return Product
     */
    public function applyModifications(int $numberOfModifications){
        return new $this($this->product, $this->modification->apply($numberOfModifications));
    }

}