<?php


namespace App\Domain\Warehouse;

final class Warehouse
{

    /**
     * @var WarehouseId
     */
    private $id;

    /**
     * @var WarehouseName
     */
    private $name;

    /**
     * @param WarehouseId   $id
     * @param WarehouseName $name
     */
    public function __construct(WarehouseId $id, WarehouseName $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function name():WarehouseName
    {
        return $this->name;
    }

    public function id(): WarehouseId
    {
        return $this->id;
    }
}
