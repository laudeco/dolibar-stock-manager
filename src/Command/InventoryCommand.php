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
     * @var \DateTimeImmutable|null
     */
    private $dlc;

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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDueDate(): \DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function getStockId(): int
    {
        return $this->stockId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function batch(string $serial, \DateTimeImmutable $dlc = null)
    {
        $this->serial = $serial;
        $this->dlc = $dlc;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function getDlc(): ?\DateTimeImmutable
    {
        return $this->dlc;
    }

    public function isBatch(): bool
    {
        return null !== $this->serial;
    }
}
