<?php


namespace App\ViewModel;

/**
 * @package App\ViewModel
 */
final class Product
{

    /**
     * @var string|null
     */
    private $codebar;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $stock;

    /**
     * @var bool
     */
    private $serialNumberable;
    /**
     * @var bool
     */
    private $stockUsage;

    /**
     * @return string
     */
    public function getCodebar(): string
    {
        return $this->codebar;
    }

    /**
     * @param string $codebar
     */
    public function setCodebar(string $codebar): void
    {
        $this->codebar = $codebar;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int $realStock
     */
    public function setStock(int $realStock)
    {
        $this->stock = $realStock;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @return bool
     */
    public function serialNumberable()
    {
        return $this->serialNumberable;
    }

    /**
     * @param bool $serialNumber
     */
    public function setSerialNumberable($serialNumber)
    {
        $this->serialNumberable = $serialNumber;
    }

    /**
     * @param bool $isBatchUsage
     */
    public function setStockUsage(bool $isBatchUsage)
    {
        $this->stockUsage = $isBatchUsage;
    }

    /**
     * @return bool
     */
    public function isStockUsage(): bool
    {
        return $this->stockUsage;
    }
}
