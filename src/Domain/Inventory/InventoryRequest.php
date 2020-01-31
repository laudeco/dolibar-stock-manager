<?php


namespace App\Domain\Inventory;

final class InventoryRequest
{

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $warehouseId;

    public function __construct(int $productId, int $warehouseId)
    {
        $this->productId = $productId;
        $this->warehouseId = $warehouseId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getWarehouseId(): int
    {
        return $this->warehouseId;
    }
}
