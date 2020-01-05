<?php

namespace App\Controller;

use App\Query\ProductQuery;
use App\Query\ProductsQueryHandler;
use http\Client\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @package App\Controller
 */
final class IndexController extends AbstractController
{

    /**
     * @var ProductsQueryHandler
     */
    private $productQueryHandler;

    /**
     * @param ProductsQueryHandler $productQueryHandler
     * @param ContainerInterface $container
     */
    public function __construct(ProductsQueryHandler $productQueryHandler, ContainerInterface $container)
    {
        $this->productQueryHandler = $productQueryHandler;
    }

    /**
     * @return Response
     */
    public function index()
    {
        return $this->render('index/index.html.twig', ['products' => $this->productQueryHandler->__invoke(new ProductQuery())]);
    }

}