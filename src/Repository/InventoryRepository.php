<?php


namespace App\Repository;

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

}