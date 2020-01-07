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
     * @param int $quantity
     * @param string $barcode
     */
    private function __construct(int $quantity, string $barcode)
    {
        if(empty($barcode)){
            throw new \InvalidArgumentException();
        }

        $this->quantity = $quantity;
        $this->barcode = $barcode;
    }

    /**
     * @param string $barcode
     * @param int $quantity
     *
     * @return StockMovement
     */
    public static function move(string $barcode, int $quantity){
        return new self($quantity, $barcode);
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

}