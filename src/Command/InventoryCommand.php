<?php


namespace App\Command;

use Webmozart\Assert\Assert;

/**
 * @package App\Command
 */
final class InventoryCommand
{

    /**
     * @var string
     */
    private $label;

    /**
     * @var \DateTimeImmutable
     */
    private $dueDate;

    /**
     * @var int
     */
    private $stockId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string|null
     */
    private $serial;

    /**
     * @var \DateTimeInterface|null
     */
    private $dlc;

    /**
     * @param string             $label
     * @param \DateTimeImmutable $dueDate
     * @param int                $stockId
     * @param int                $productId
     * @param int                $quantity
     */
    public function __construct(string $label, \DateTimeImmutable $dueDate, int $stockId, int $productId, int $quantity)
    {
        Assert::notEmpty($label);
        Assert::greaterThan($stockId, 0);
        Assert::greaterThan($productId, 0);

        $this->label = $label;
        $this->dueDate = $dueDate;
        $this->stockId = $stockId;

        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDueDate(): \DateTimeImmutable
    {
        return $this->dueDate;
    }

    /**
     * @return int
     */
    public function getStockId(): int
    {
        return $this->stockId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param string                  $serial
     * @param \DateTimeImmutable|null $dlc
     */
    public function batch(string $serial, \DateTimeImmutable $dlc = null)
    {
        $this->serial = $serial;
        $this->dlc = $dlc;
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
}
