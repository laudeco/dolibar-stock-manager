<?php


namespace App\Controller;

use App\Infrastructure\Security\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package App\Controller
 */
final class LoginController extends AbstractController
{

    /**
     * @Route("/login", name="login", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function index()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json(['login' => $user->getUsername()]);
    }
}