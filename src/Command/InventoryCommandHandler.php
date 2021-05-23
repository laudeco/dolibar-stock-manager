<?php


namespace App\Command;

use App\Application\Inventory\Requester\InventoryRequesterInterface;
use App\Domain\Product\Counter;
use App\Domain\Product\Product;
use App\Domain\Product\ProductId;
use App\Exception\InventoryCheckRequestedException;
use App\Exception\ProductNotFoundException;
use App\Repository\Dolibarr\StockMovementRepository;
use App\Repository\ProductRepository;
use Dolibarr\Client\Domain\StockMovement\StockMovement;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use App\Repository\Dolibarr\ProductRepository as DolibarrProductRepository;
use App\ViewModel\Product as DolibarrProduct;

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
     * @var DolibarrProductRepository
     */
    private $dolibarrProductRepository;

    /**
     * @var InventoryRequesterInterface
     */
    private $inventoryRequester;

    public function __construct(
        StockMovementRepository $stockService,
        ProductRepository $productRepository,
        DolibarrProductRepository $dolibarrProductRepository,
        InventoryRequesterInterface $inventoryRequester
    ) {
        $this->stockMovementRepository = $stockService;
        $this->productRepository = $productRepository;
        $this->dolibarrProductRepository = $dolibarrProductRepository;

        $this->inventoryRequester = $inventoryRequester;
    }


    /**
     * @param InventoryCommand $command
     *
     * @throws \Dolibarr\Client\Exception\ApiException
     * @throws InventoryCheckRequestedException
     * @throws ProductNotFoundException
     */
    public function __invoke(InventoryCommand $command): void
    {
        try {
            $product = $this->dolibarrProductRepository->getById($command->getProductId());
        } catch (\Dolibarr\Client\Exception\ResourceNotFoundException $e) {
            throw new ProductNotFoundException();
        }

        $this->persist($command);

        if ($product->serialNumberable()) {
            return;
        }

        $numberOfMovements = $this->increaseUpdate($command->getProductId());
        $this->inventoryCheck($product, $numberOfMovements);
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

        if ($command->isBatch()) {
            $dolibarrMovement->setLot($command->getSerial());

            if (null !== $command->getDlc()) {
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
     * @param DolibarrProduct $product
     * @param int             $counter
     *
     * @throws InventoryCheckRequestedException
     */
    private function inventoryCheck(DolibarrProduct $product, int $counter): void
    {
        if (!$this->inventoryRequester->shouldTriggerInventory($product, $counter)) {
            return;
        }

        throw new InventoryCheckRequestedException($product->getId());
    }
}
