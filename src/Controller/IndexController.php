<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $login = $request->get('l', $request->get('login', ''));
        return $this->render('index/index.html.twig', ['login' => $login]);
    }
}
