<?php


namespace App\Application\Inventory\Requester;

use App\Domain\Inventory\Inventory;
use App\Domain\Inventory\Quantity;
use App\Repository\InventoryRepository;
use App\ViewModel\Product;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

final class InventoryRequester implements InventoryRequesterInterface
{

    /**
     * @var InventoryRepository
     */
    private $inventoryRepository;

    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;

    public function __construct(InventoryRepository $inventoryRepository, int $min, int $max)
    {
        $this->inventoryRepository = $inventoryRepository;
        $this->min = $min;
        $this->max = $max;
    }

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
        try {
            $inventory = $this->inventoryRepository->getById($product->getId());
        } catch (ResourceNotFoundException $e) {
            $inventory = $this->createRandomInventory($product->getId());
        }

        if (!$inventory->isLimitReached($counter)) {
            return false;
        }

        $this->createRandomInventory($product->getId());

        return true;
    }


    /**
     * @param int $productId
     *
     * @return Inventory
     */
    private function createRandomInventory(int $productId): Inventory
    {
        $inventory = Inventory::forProduct(
            new \App\Domain\Inventory\Product($productId),
            Quantity::random($this->min, $this->max)
        );

        $this->inventoryRepository->save($inventory);

        return $inventory;
    }
}
