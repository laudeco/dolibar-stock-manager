<?php


namespace App\Application\Inventory\Requester;

use App\ViewModel\Product;

interface InventoryRequesterInterface
{
    /**
     * Is an inventory must be triggered for this product and this number of actions.
     *
     * @param Product $product
     * @param int     $counter
     *
     * @return bool
     */
    public function shouldTriggerInventory(Product $product, int $counter = 1): bool;
}
