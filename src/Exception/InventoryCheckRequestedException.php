<?php


namespace App\Exception;

/**
 * Exception to notice that a product requires an inventory check.
 *
 * @package App\Exception
 */
final class InventoryCheckRequestedException extends \Exception
{

    /**
     * @var int
     */
    private $productId;

    /**
     * @param int $productId
     */
    public function __construct(int $productId)
    {
        parent::__construct('');
        $this->productId = $productId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

}