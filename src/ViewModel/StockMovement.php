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
     * @var string|null
     */
    private $serial;

    /**
     * @var \DateTimeImmutable|null
     */
    private $dlc;

    /**
     * @param int $quantity
     * @param string $barcode
     * @param int $productId
     * @param string|null $serial
     * @param \DateTimeImmutable|null $dlc
     */
    private function __construct(int $quantity, string $barcode, int $productId, string $serial = null, \DateTimeImmutable $dlc = null)
    {
        if (empty($barcode)) {
            throw new \InvalidArgumentException();
        }

        $this->quantity = $quantity;
        $this->barcode = $barcode;
        $this->productId = $productId;
        $this->serial = $serial;
        $this->dlc = $dlc;

    }

    /**
     * @param string $barcode
     * @param int $productId
     * @param int $quantity
     *
     * @return StockMovement
     */
    public static function move(string $barcode, int $productId, int $quantity)
    {
        return new self($quantity, $barcode, $productId);
    }

    /**
     * @param string $barcode
     * @param int $productId
     * @param int $quantity
     * @param string $serial
     * @param \DateTimeImmutable $dlc
     *
     * @return StockMovement
     */
    public static function batch(string $barcode, int $productId, int $quantity, string $serial = null, \DateTimeImmutable $dlc = null)
    {
        return new self($quantity, $barcode, $productId,$serial, $dlc);
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

    /**
     * @return string|null
     */
    public function getSerial(): ?string
    {
        return $this->serial;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDlc(): ?\DateTimeInterface
    {
        return $this->dlc;
    }

    /**
     * @return bool
     */
    public function isBatch(){
        return null !== $this->serial;
    }

}
