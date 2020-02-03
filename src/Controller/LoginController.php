<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @package App\Controller
 */
final class LoginController extends AbstractController
{

    /**
     * @Route("/login", name="login_screen", methods={"GET"})
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param Request             $request
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
        $login = $request->get('l', $request->get('login', ''));
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('login/index.html.twig', ['login' => $login, 'error' => $error]);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function index()
    {
        return $this->redirectToRoute('index');
    }
}
