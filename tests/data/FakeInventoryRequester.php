<?php


namespace App\Tests\data;

use App\Application\Inventory\Requester\InventoryRequesterInterface;
use App\ViewModel\Product;

final class FakeInventoryRequester implements InventoryRequesterInterface
{

    /**
     * Is an inventory must be triggered for this product and this number of actions.
     *
     * @param Product $product
     * @param int     $counter
     *
     * @return bool
     */
    public function shouldTriggerInventory(Product $product, int $counter = 1): bool
    {
        return true;
    }
}
