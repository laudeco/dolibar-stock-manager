<?php


namespace App\Query;

use App\Repository\Dolibarr\WarehouseRepositoryInterface;
use App\ViewModel\Warehouse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class GetWarehousesQueryHandler
{
    /**
     * @var WarehouseRepositoryInterface
     */
    private $warehouseRepository;

    public function __construct(WarehouseRepositoryInterface $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * @param GetWarehousesQuery $query
     *
     * @return Collection|Warehouse[]
     */
    public function __invoke(GetWarehousesQuery $query):Collection
    {
        $warehouses = $this->warehouseRepository->list($query->getPage(), $query->getLimit());

        $viewWarehouses = new ArrayCollection([]);

        /** @var \App\Domain\Warehouse\Warehouse $warehouse */
        foreach ($warehouses as $warehouse) {
            $viewWarehouses[] = new Warehouse($warehouse->name()->getName(), $warehouse->id()->getId());
        }

        return $viewWarehouses;
    }
}
