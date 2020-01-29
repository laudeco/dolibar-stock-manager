<?php


namespace App\Repository\Dolibarr;

interface WarehouseRepositoryInterface
{
    public function list(int $page, int $limit);
}
