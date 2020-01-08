<?php


namespace App\Repository\Dolibarr;

use Dolibarr\Client\Domain\StockMovement\StockMovement;
use Dolibarr\Client\Domain\StockMovement\StockMovementId;
use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Service\StockMovementsService;

/**
 * @package App\Repository\Dolibarr
 */
final class StockMovementRepository extends DolibarrRepository
{
    /**
     * @var StockMovementsService
     */
    private $productService;

    /**
     * @return StockMovementsService
     */
    protected function service()
    {
        if (null !== $this->productService) {
            return $this->productService;
        }

        return $this->client()->stockMovements();
    }

    /**
     * @param StockMovement $movement
     *
     * @return StockMovementId
     *
     * @throws ApiException
     */
    public function save(StockMovement $movement)
    {
        return $this->service()->create($movement);
    }
}
