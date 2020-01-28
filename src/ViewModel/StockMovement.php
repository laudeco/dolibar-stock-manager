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
     * @var string
     */
    private $productLabel;

    /**
     * @var bool
     */
    private $issue;

    /**
     * @param string                  $label
     * @param int                     $quantity
     * @param string                  $barcode
     * @param int                     $productId
     * @param string|null             $serial
     * @param \DateTimeImmutable|null $dlc
     */
    private function __construct(string $label, int $quantity, string $barcode, int $productId, string $serial = null, \DateTimeImmutable $dlc = null)
    {
        if (empty($barcode)) {
            throw new \InvalidArgumentException();
        }

        $this->productLabel = $label;
        $this->quantity = $quantity;
        $this->barcode = $barcode;
        $this->productId = $productId;
        $this->serial = $serial;
        $this->dlc = $dlc;
        $this->issue = false;
    }

    /**
     * @param string $label
     * @param string $barcode
     * @param int    $productId
     * @param int    $quantity
     *
     * @return StockMovement
     */
    public static function move(string $label, string $barcode, int $productId, int $quantity)
    {
        return new self($label, $quantity, $barcode, $productId);
    }

    /**
     * @param string             $label
     * @param string             $barcode
     * @param int                $productId
     * @param int                $quantity
     * @param string             $serial
     * @param \DateTimeImmutable $dlc
     *
     * @return StockMovement
     */
    public static function batch(string $label, string $barcode, int $productId, int $quantity, string $serial, \DateTimeImmutable $dlc = null)
    {
        if (empty($serial)) {
            throw new \InvalidArgumentException('Serial cannot be empty!');
        }

        return new self($label, $quantity, $barcode, $productId, $serial, $dlc);
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
