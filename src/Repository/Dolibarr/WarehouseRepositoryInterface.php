<?php


namespace App\Repository\Dolibarr;

use App\Domain\Warehouse\WarehouseCollection;

interface WarehouseRepositoryInterface
{
    public function list(int $page, int $limit): WarehouseCollection;
}
