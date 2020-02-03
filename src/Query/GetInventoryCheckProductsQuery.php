<?php


namespace App\Query;

final class GetInventoryCheckProductsQuery
{

    /**
     * @var int
     */
    private $warehouseId;

    /**
     * @var int
     */
    private $productId;

    public function __construct(int $warehouseId, int $productId)
    {
        $this->warehouseId = $warehouseId;
        $this->productId = $productId;
    }

    /**
     * @return int
     */
    public function getWarehouseId(): int
    {
        return $this->warehouseId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }
}
