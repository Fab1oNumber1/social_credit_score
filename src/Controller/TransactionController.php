<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\TransactionType;
use App\Repository\UserRepository;
use App\Service\ScoreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    #[Route('/transaction/view/{user}', name: 'app_transaction')]
    public function index(User $user): Response
    {
        return $this->render('transaction/index.html.twig', [
            'user' => $user,
            'transactions' => $user->getTransactions(),
        ]);
    }
    #[Route('/transaction/create', name: 'app_transaction_create')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Security $security,
        ScoreService $scoreService,
    ): Response
    {
        $transaction = new Transaction();
        if($request->get('user') && $user = $userRepository->find($request->get('user'))) {
            $transaction->setUser($user);
        }
        $transaction->setAuthor($security->getUser());
        $transaction = $scoreService->approve($security->getUser(), $transaction);
        $form = $this->createForm(TransactionType::class, $transaction);



        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $transaction = $form->getData();

            $entityManager->persist($transaction);
            $entityManager->flush();
            $this->addFlash("success", "Transaktion erstellt.");
            return $this->redirectToRoute('app_transaction', ['user' => $transaction->getUser()->getId()]);
        }

        return $this->render('transaction/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/transaction/{transaction}/approve', name: 'app_transaction_approve')]
    public function approve(
        Transaction $transaction,
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Security $security,
        ScoreService $scoreService,
    ): Response
    {

        if(!$scoreService->canApprove( $security->getUser(), $transaction)) {
            throw new \Exception("Kei Recht du arsch");
        }

        $transaction = $scoreService->approve($security->getUser(), $transaction);


        $entityManager->persist($transaction);
        $entityManager->flush();

        $this->addFlash("success", "Transaktion akzeptiert");
        return $this->redirectToRoute('app_transaction', ['user' => $transaction->getUser()->getId()]);

    }
}
