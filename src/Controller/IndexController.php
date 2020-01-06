<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package App\Controller
 */
final class IndexController extends AbstractController
{

    /**
     * @return Response
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }

}