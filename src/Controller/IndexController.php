<?php

namespace App\Controller;

use http\Client\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @package App\Controller
 */
final class IndexController extends AbstractController
{

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {

    }

    /**
     * @return Response
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }

}