<?php


namespace App\ViewModel;

final class ProductInventory
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $barcode;

    /**
     * @var string|null
     */
    private $label;

    /**
     * @var bool
     */
    private $find;

    /**
     * @var int
     */
    private $warehouse;

    private function __construct(int $id, int $warehouse, ?string $barcode = null, ?string $label = null)
    {
        $this->id = $id;
        $this->barcode = $barcode;
        $this->label = $label;
        $this->find = !(null === $label && null === $barcode);
        $this->warehouse = $warehouse;
    }

    /**
     * @param int $id
     *
     * @return ProductInventory
     */
    public static function notFound(int $id)
    {
        return new self($id, 0);
    }

    /**
     * @param int    $id
     * @param string $barcode
     * @param string $label
     * @param int    $warehouse
     *
     * @return ProductInventory
     */
    public static function create(int $id, string $barcode, string $label, int $warehouse)
    {
        return new self($id, $warehouse, $barcode, $label);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isFound(): bool
    {
        return $this->find;
    }

    /**
     * @return int
     */
    public function getWarehouse(): int
    {
        return $this->warehouse;
    }
}
