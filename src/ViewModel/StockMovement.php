<?php

namespace App\ViewModel;

/**
 * @package App\ViewModel
 */
final class StockMovement
{

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $barcode;

    /**
     * @var int
     */
    private $productId;

    /**
     * @param int    $quantity
     * @param string $barcode
     * @param int    $productId
     */
    private function __construct(int $quantity, string $barcode, int $productId)
    {
        if (empty($barcode)) {
            throw new \InvalidArgumentException();
        }

        $this->quantity = $quantity;
        $this->barcode = $barcode;
        $this->productId = $productId;
    }

    /**
     * @param string $barcode
     * @param int    $productId
     * @param int    $quantity
     *
     * @return StockMovement
     */
    public static function move(string $barcode, int $productId, int $quantity)
    {
        return new self($quantity, $barcode, $productId);
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getBarcode(): string
    {
        return $this->barcode;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }
}
