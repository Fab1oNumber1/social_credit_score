<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Transaction;
use App\Entity\TransactionComment;
use App\Entity\User;
use App\Form\TransactionCommentType;
use App\Form\TransactionType;
use App\Repository\UserRepository;
use App\Service\NotificationService;
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

    #[Route('/transaction/single/{transaction}', name: 'app_transaction_view')]
    public function viewSingle(
        Transaction $transaction,
        Security $security,
        Request $request,
        EntityManagerInterface $entityManager,
        NotificationService $notificationService,
    ): Response
    {
        $transactionComment = new TransactionComment();
        $transactionComment->setAuthor($security->getUser());
        $transactionComment->setTransaction($transaction);
        $commentForm = $this->createForm(TransactionCommentType::class, $transactionComment);

        $commentForm->handleRequest($request);
        if($commentForm->isSubmitted() && $commentForm->isValid()) {
            $transactionComment = $commentForm->getData();
            $entityManager->persist($transactionComment);
            $entityManager->flush();
            $this->addFlash("success", "Dini Meinig isch wertvoll für eus");
            $notificationService->notify($transactionComment);

            $transactionComment = new TransactionComment();
            $transactionComment->setAuthor($security->getUser());
            $transactionComment->setTransaction($transaction);
            $commentForm = $this->createForm(TransactionCommentType::class, $transactionComment);
        }

        return $this->render('transaction/view.html.twig', [
            'commentForm' => $commentForm,
            'transaction' => $transaction,
        ]);
    }
    #[Route('/transaction/create', name: 'app_transaction_create')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Security $security,
        ScoreService $scoreService,
        NotificationService $notificationService,
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
            $notificationService->notify($transaction);
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
