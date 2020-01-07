<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 */
final class IndexController extends AbstractController
{

    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }

}