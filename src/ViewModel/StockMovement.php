<?php

namespace App\ViewModel;

use App\Domain\Product\Barcode;
use App\Domain\Product\LimitDate;
use App\Domain\Product\ProductId;
use App\Domain\Product\Quantity;
use App\Domain\Product\Serial;
use App\Domain\Warehouse\WarehouseId;
use Webmozart\Assert\Assert;

/**
 * @package App\ViewModel
 */
final class StockMovement
{

    /**
     * @var Quantity
     */
    private $quantity;

    /**
     * @var Barcode
     */
    private $barcode;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var Serial|null
     */
    private $serial;

    /**
     * @var LimitDate|null
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
     * @var WarehouseId
     */
    private $warehouse;

    /**
     * @param WarehouseId    $warehouseId
     * @param string         $label
     * @param Quantity       $quantity
     * @param Barcode        $barcode
     * @param ProductId      $productId
     * @param Serial|null    $serial
     * @param LimitDate|null $dlc
     */
    private function __construct(WarehouseId $warehouseId, string $label, Quantity $quantity, Barcode $barcode, ProductId $productId, Serial $serial = null, LimitDate $dlc = null)
    {
        Assert::stringNotEmpty($label, 'Label must be present');

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
        return new self(WarehouseId::create($warehouseId), $label, Quantity::create($quantity), Barcode::initialize($barcode), new ProductId($productId));
    }

    public static function batch(int $warehouseId, string $label, string $barcode, int $productId, int $quantity, string $serial, \DateTimeImmutable $dlc = null):StockMovement
    {
        if ($quantity > 0) {
            Assert::notNull($dlc, 'The end date must be present');
        }

        if ($quantity < 0) {
            return new self(WarehouseId::create($warehouseId), $label, Quantity::create($quantity), Barcode::initialize($barcode), new ProductId($productId), Serial::initialize($serial));
        }

        return new self(WarehouseId::create($warehouseId), $label, Quantity::create($quantity), Barcode::initialize($barcode), new ProductId($productId), Serial::initialize($serial), LimitDate::fromDate($dlc));
    }

    /**
     * @return int
     */
    public function getWarehouse(): int
    {
        return $this->warehouse->getId();
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity->getValue();
    }

    /**
     * @return string
     */
    public function getBarcode(): string
    {
        return $this->barcode->getValue();
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId->getId();
    }

    /**
     * @return string|null
     */
    public function getSerial(): ?string
    {
        return $this->serial->getValue();
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDlc(): ?\DateTimeInterface
    {
        return $this->dlc ? $this->dlc->getValue() : null;
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
