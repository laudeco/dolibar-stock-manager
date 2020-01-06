<?php


namespace App\Repository;

use App\Domain\Inventory\Inventory;
use App\Domain\Inventory\Product;
use App\Domain\Inventory\Quantity;

/**
 * @package App\Repository
 */
final class InventoryRepository
{

    /**
     * @var DbManager
     */
    private $dbManager;

    /**
     * @param DbManager $dbManager
     */
    public function __construct(DbManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    /**
     * @param Inventory $inventory
     */
    public function save(Inventory $inventory)
    {
        $this->dbManager->save($this->fromEntity($inventory));
    }

    /**
     * @param string $id
     *
     * @return Inventory
     */
    public function getById(string $id)
    {
        $inventory = $this->dbManager->getById($id);
        return $this->toEntity($inventory);
    }

    /**
     * @param Inventory $inventory
     *
     * @return array
     */
    private function fromEntity(Inventory $inventory)
    {
        return [
            'product' => $inventory->getProduct()->getId(),
            'quantity' => $inventory->getQuantity()->getValue(),
        ];
    }

    /**
     * @param array $inventory
     *
     * @return Inventory
     */
    private function toEntity(array $inventory)
    {
        return Inventory::forProduct(new Product($inventory['product']), Quantity::create($inventory['quantity']));
    }
}