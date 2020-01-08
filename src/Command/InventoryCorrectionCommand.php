<?php


namespace App\Command;

use Webmozart\Assert\Assert;

final class InventoryCorrectionCommand
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
}
