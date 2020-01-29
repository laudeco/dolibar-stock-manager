<?php


namespace App\Repository\Dolibarr;

use App\Domain\Warehouse\Warehouse;
use App\Domain\Warehouse\WarehouseCollection;
use App\Domain\Warehouse\WarehouseId;
use App\Domain\Warehouse\WarehouseName;
use App\Exception\WarehouseNotFoundException;
use Dolibarr\Client\Exception\ApiException;
use Dolibarr\Client\Service\WarehousesService;
use Exception;

final class WarehouseRepository extends DolibarrRepository implements WarehouseRepositoryInterface
{

    /**
     * @var WarehousesService
     */
    private $warehouseService;

    protected function service(): WarehousesService
    {
        if (null !== $this->warehouseService) {
            return $this->warehouseService;
        }

        return $this->client()->warehouse();
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return WarehouseCollection
     *
     * @throws WarehouseNotFoundException
     * @throws Exception
     */
    public function list(int $page, int $limit):WarehouseCollection
    {
        try {
            $domainWarehouses = WarehouseCollection::instanciate();
            $warehouses = $this->warehouseService->findAll($limit, $page - 1);

            foreach ($warehouses as $currentWarehouse) {
                $domainWarehouses = $domainWarehouses->add(new Warehouse(WarehouseId::create($currentWarehouse->getId()), WarehouseName::name($currentWarehouse->getLabel())));
            }

            if ($domainWarehouses->empty()) {
                throw new WarehouseNotFoundException();
            }

            return $domainWarehouses;
        } catch (ApiException $e) {
            throw new Exception('', 0, $e);
        }
    }
}
