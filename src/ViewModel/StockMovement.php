<?php

namespace App\ViewModel;

use Webmozart\Assert\Assert;

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
     * @var string
     */
    private $productLabel;

    /**
     * @var bool
     */
    private $issue;

    /**
     * @var int
     */
    private $warehouse;

    /**
     * @param int $warehouseId
     * @param string $label
     * @param int $quantity
     * @param string $barcode
     * @param int $productId
     * @param string|null $serial
     * @param \DateTimeImmutable|null $dlc
     */
    private function __construct(int $warehouseId, string $label, int $quantity, string $barcode, int $productId, string $serial = null, \DateTimeImmutable $dlc = null)
    {
        Assert::stringNotEmpty($barcode, 'Barcode must be present');
        Assert::stringNotEmpty($label, 'Label must be present');
        Assert::notEq($quantity, 0, 'Quantity cannot be 0');

        $this->warehouse = $warehouseId;
        $this->productLabel = $label;
        $this->quantity = $quantity;
        $this->barcode = $barcode;
        $this->productId = $productId;
        $this->serial = $serial;
        $this->dlc = $dlc;
        $this->issue = false;
    }

    public static function move(int $warehouseId, string $label, string $barcode, int $productId, int $quantity): StockMovement
    {
        return new self($warehouseId, $label, $quantity, $barcode, $productId);
    }

    public static function batch(int $warehouseId, string $label, string $barcode, int $productId, int $quantity, string $serial, \DateTimeImmutable $dlc = null):StockMovement
    {
        Assert::stringNotEmpty($serial);

        if($quantity > 0){
            Assert::notNull($dlc, 'The end date must be present');
        }

        if($quantity < 0){
            $dlc = null;
        }

        return new self($warehouseId, $label, $quantity, $barcode, $productId, $serial, $dlc);
    }

    /**
     * @return int
     */
    public function getWarehouse(): int
    {
        return $this->warehouse;
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
    public function isBatch()
    {
        return null !== $this->serial;
    }

    /**
     * Flags this movement as in error.
     */
    public function fail()
    {
        $this->issue = true;
    }

    /**
     * Is this movement in issue?
     */
    public function failed(): bool
    {
        return $this->issue;
    }

    /**
     * @return string
     */
    public function getProductLabel(): string
    {
        return $this->productLabel;
    }
}
