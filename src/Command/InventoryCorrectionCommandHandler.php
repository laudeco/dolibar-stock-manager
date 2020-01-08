<?php


namespace App\Command;

use App\Repository\Dolibarr\ProductRepository;
use App\Repository\Dolibarr\StockMovementRepository;
use Dolibarr\Client\Domain\StockMovement\StockMovement;
use Dolibarr\Client\Exception\ApiException;

/**
 * @package App\Command
 */
final class InventoryCorrectionCommandHandler
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
     * @param StockMovementRepository $stockMovementRepository
     * @param ProductRepository       $productRepository
     */
    public function __construct(StockMovementRepository $stockMovementRepository, ProductRepository $productRepository)
    {
        $this->stockMovementRepository = $stockMovementRepository;
        $this->productRepository = $productRepository;
    }


    /**
     * @param InventoryCorrectionCommand $command
     *
     * @throws ApiException
     */
    public function __invoke(InventoryCorrectionCommand $command)
    {
        $product = $this->productRepository->getById($command->getProductId());

        if ($product->getStock() === $command->getQuantity()) {
            return;
        }

        $dolibarrMovement = new StockMovement();

        $dolibarrMovement->setProductId($command->getProductId());
        $dolibarrMovement->setWarehouseId($command->getStockId());

        $dolibarrMovement->setQuantity($command->getQuantity() - $product->getStock());

        $dolibarrMovement->setLabel($command->getLabel());
        $dolibarrMovement->setInventoryCode($command->getDueDate()->format(DATE_ATOM));

        $this->stockMovementRepository->save($dolibarrMovement);
    }
}
