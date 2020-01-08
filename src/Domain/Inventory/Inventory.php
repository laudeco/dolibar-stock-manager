<?php


namespace App\Domain\Inventory;

/**
 * @package App\Entity
 */
final class Inventory
{

    /**
     * @var Product
     */
    private $product;

    /**
     * @var Quantity
     */
    private $quantity;

    /**
     * @param Product $product
     * @param Quantity $quantity
     */
    private function __construct(Product $product, Quantity $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * @param Product $product
     * @param Quantity $quantity
     *
     * @return Inventory
     */
    public static function forProduct(Product $product, Quantity $quantity){
        return new self($product, $quantity);
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return Quantity
     */
    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    /**
     * @param int $counter
     *
     * @return bool
     */
    public function isLimitReached(int $counter): bool
    {
        return $this->quantity->isLimitReached($counter);
    }

}