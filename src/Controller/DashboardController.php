<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_start')]
    public function start(
        UserRepository $userRepository,
    ): Response
    {
        return $this->redirectToRoute('app_login');
    }
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        UserRepository $userRepository,
    ): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
}
