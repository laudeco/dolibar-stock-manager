<?php


namespace App\Command;

use App\Domain\Inventory\Inventory;
use App\Domain\Inventory\Quantity;
use App\Domain\Product\Counter;
use App\Domain\Product\Product;
use App\Domain\Product\ProductId;
use App\Exception\InventoryCheckRequestedException;
use App\Repository\Dolibarr\StockMovementRepository;
use App\Repository\InventoryRepository;
use App\Repository\ProductRepository;
use Dolibarr\Client\Domain\StockMovement\StockMovement;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @package App\Command
 */
final class InventoryCommandHandler
{

    /**
     * @var StockMovementRepository
     */
    private $stockMovementRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

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

    /**
     * @param StockMovementRepository $stockService
     * @param ProductRepository       $productRepository
     * @param InventoryRepository     $inventoryRepository
     * @param int                     $min
     * @param int                     $max
     */
    public function __construct(
        StockMovementRepository $stockService,
        ProductRepository $productRepository,
        InventoryRepository $inventoryRepository,
        int $min,
        int $max
    ) {
        $this->stockMovementRepository = $stockService;
        $this->productRepository = $productRepository;
        $this->inventoryRepository = $inventoryRepository;
        $this->min = $min;
        $this->max = $max;
    }


    /**
     * @param InventoryCommand $command
     *
     * @throws \Dolibarr\Client\Exception\ApiException
     * @throws InventoryCheckRequestedException
     */
    public function __invoke(InventoryCommand $command)
    {
        $this->persist($command);

        $numberOfMovements = $this->increaseUpdate($command->getProductId());

        $this->inventoryCheck($command->getProductId(), $numberOfMovements);
    }

    /**
     * Persists those movements on Dolibarr.
     *
     * @param InventoryCommand $command
     *
     * @throws \Dolibarr\Client\Exception\ApiException
     */
    private function persist(InventoryCommand $command)
    {
        $dolibarrMovement = new StockMovement();

        $dolibarrMovement->setProductId($command->getProductId());
        $dolibarrMovement->setWarehouseId($command->getStockId());

        $dolibarrMovement->setQuantity($command->getQuantity());

        $dolibarrMovement->setLabel($command->getLabel());
        $dolibarrMovement->setInventoryCode($command->getDueDate()->format(DATE_ATOM));

        if($command->isBatch()){
            $dolibarrMovement->setLot($command->getSerial());

            if(null !== $command->getDlc()){
                $dolibarrMovement->setDlc($command->getDlc());
            }
        }


        $this->stockMovementRepository->save($dolibarrMovement);
    }

    /**
     * Increases the number of stock movement for one product.
     * Returns the number of new movements.
     *
     * @param int $productId
     *
     * @return int
     */
    private function increaseUpdate(int $productId): int
    {
        try {
            $product = $this->productRepository->getById($productId);
            $product = $product->applyModifications(1);
        } catch (ResourceNotFoundException $e) {
            $product = new Product(new ProductId($productId), Counter::start());
        }

        $this->productRepository->save($product);

        return $product->getCounter()->getValue();
    }

    /**
     * @param int $productId
     * @param int $counter
     *
     * @throws InventoryCheckRequestedException
     */
    private function inventoryCheck(int $productId, int $counter): void
    {
        try {
            $inventory = $this->inventoryRepository->getById($productId);
        } catch (ResourceNotFoundException $e) {
            $inventory = $this->createRandomInventory($productId);
        }

        if (!$inventory->isLimitReached($counter)) {
            return;
        }

        $this->createRandomInventory($productId);

        throw new InventoryCheckRequestedException($productId);
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
