<?php


namespace App\Factory;

use App\Application\Inventory\Requester\InventoryRequesterInterface;
use App\Application\Inventory\Requester\NoInventoryRequester;

final class InventoryRequesterFactory
{

    /**
     * @var bool
     */
    private $inventoryRequest;

    /**
     * @var InventoryRequesterInterface
     */
    private $default;

    public function __construct(bool $inventoryRequest, InventoryRequesterInterface $default)
    {
        $this->inventoryRequest = $inventoryRequest;
        $this->default = $default;
    }

    /**
     * @return InventoryRequesterInterface
     */
    public function create(): InventoryRequesterInterface
    {
        if (!$this->inventoryRequest) {
            return new NoInventoryRequester();
        }

        return $this->default;
    }
}
