<?php


namespace App\Application\Inventory\Requester;

use App\ViewModel\Product;

/**
 * This inventory manager always returns false. Should be used when there is no inventory management.
 *
 * @package App\Application\Inventory\Requester
 */
final class NoInventoryRequester implements InventoryRequesterInterface
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
        return false;
    }
}
