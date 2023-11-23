<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use App\Service\ScoreService;
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
        if($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');

        }
        return $this->redirectToRoute('app_login');
    }
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        UserRepository $userRepository,
        TransactionRepository $transactionRepository,
        ScoreService $scoreService,
    ): Response
    {
        $reviewTransactions = $transactionRepository->findBy(['status' => 'review', 'active' => 1], ['created' => 'DESC']);
        $users = $userRepository->findAll();
        usort($users, fn($a, $b) => $scoreService->calculate($b) <=> $scoreService->calculate($a));
        return $this->render('dashboard/index.html.twig', [
            'reviewTransactions' => $reviewTransactions,
            'users' => $users,
        ]);
    }
}
