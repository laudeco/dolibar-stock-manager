<?php


namespace App\Controller;

use App\Query\GetWarehousesQueryHandler;
use App\Query\QueryHandlerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/warehouses", name="warehouses_")
 *
 * @package App\Controller
 */
final class ApiWarehousesListController
{

    /**
     * @var GetWarehousesQueryHandler
     */
    private $queryHandler;

    /**
     * @param GetWarehousesQueryHandler $queryHandler
     */
    public function __construct(GetWarehousesQueryHandler $queryHandler)
    {
        $this->queryHandler = $queryHandler;
    }

    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function get()
    {
        $warehouses = $this->queryHandler->__invoke(new GetWarehousesQuery());
    }
}
