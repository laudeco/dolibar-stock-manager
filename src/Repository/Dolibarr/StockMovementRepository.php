<?php


namespace App\Repository\Dolibarr;

use Dolibarr\Client\Domain\StockMovement\StockMovement;
use Dolibarr\Client\Domain\StockMovement\StockMovementId;
use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Service\StockMovementsService;

/**
 * @package App\Repository\Dolibarr
 */
class StockMovementRepository extends DolibarrRepository
{
    /**
     * @var StockMovementsService
     */
    private $stockMovementsService;

    protected function service(): StockMovementsService
    {
        if (null !== $this->stockMovementsService) {
            return $this->stockMovementsService;
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
    public function save(StockMovement $movement): StockMovementId
    {
        return $this->service()->create($movement);
    }
}
