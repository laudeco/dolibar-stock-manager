<?php

namespace App\Controller;

use App\Query\GetWarehousesQuery;
use App\Query\GetWarehousesQueryHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 */
final class IndexController extends AbstractController
{

    /**
     * @var GetWarehousesQueryHandler
     */
    private $warehousesQuery;

    public function __construct(GetWarehousesQueryHandler $warehousesQuery)
    {
        $this->warehousesQuery = $warehousesQuery;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index()
    {
        $warehouses = $this->warehousesQuery->__invoke(new GetWarehousesQuery());

        return $this->render('index/index.html.twig', [
            'warehouses' => $warehouses
        ]);
    }
}
