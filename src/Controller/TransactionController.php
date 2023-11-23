<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Transaction;
use App\Entity\TransactionComment;
use App\Entity\User;
use App\Form\TransactionCommentType;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use App\Service\MediaService;
use App\Service\NotificationService;
use App\Service\ScoreService;
use App\Service\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    #[Route('/transaction/view/{user}', name: 'app_transaction')]
    public function index(
        User $user,
        TransactionRepository $transactionRepository,
    ): Response
    {
        return $this->render('transaction/index.html.twig', [
            'user' => $user,
            'transactions' => $transactionRepository->findBy(['user' => $user->getId(), 'active' => 1], ['created' => 'DESC']),
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
            $notificationService->notify("{$transactionComment->getAuthor()} hat ein Kommentar geschrieben.", $transactionComment, $transactionComment->getAuthor());

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
        MediaService $mediaService,
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
            $media = $form->get('media')->getData();
            if($media) {
                $media =  $mediaService->handleMediaUpload($media, $user, "Media zu Eintrag ".$transaction->getId());
                $transaction->setMedia($media);
                $entityManager->persist($media);
            }


            $entityManager->persist($transaction);
            $entityManager->flush();
            $this->addFlash("success", "Transaktion erstellt.");
            $notificationService->notify("{$transaction->getAuthor()} hat einen Eintrag zu {$transaction->getUser()} erstellt.", $transaction, $transaction->getAuthor());
            return $this->redirectToRoute('app_transaction', ['user' => $transaction->getUser()->getId()]);
        }

        return $this->render('transaction/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/transaction/{transaction}}/edit', name: 'app_transaction_edit')]
    public function edit(
        Transaction $transaction,
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Security $security,
        ScoreService $scoreService,
        NotificationService $notificationService,
        MediaService $mediaService,
    ): Response
    {
        if($transaction->getAuthor()->getId() !== $this->getUser()->getId()) {
            throw new \Exception("Du bisch ned de Author du Schlingel");
        }
        $form = $this->createForm(TransactionType::class, $transaction, ['mode' => 'edit']);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var Transaction $transaction */
            $transaction = $form->getData();
            $media = $form->get('media')->getData();
            if($media) {
                $media =  $mediaService->handleMediaUpload($media, $this->getUser(), "Media zu Eintrag ".$transaction->getId());
                $transaction->setMedia($media);
                $entityManager->persist($media);
            }

            foreach($transaction->getApprovers() as $approver) {
                $transaction->removeApprover($approver);
            }
            $transaction->addApprover($transaction->getAuthor());



            $entityManager->persist($transaction);
            $entityManager->flush();
            $this->addFlash("success", "Eintrag bearbeitet.");
            $notificationService->notify("{$transaction->getAuthor()} hat einen Eintrag zu {$transaction->getUser()} bearbeitet.", $transaction, $transaction->getAuthor());
            return $this->redirectToRoute('app_transaction', ['user' => $transaction->getUser()->getId()]);
        }

        return $this->render('transaction/edit.html.twig', [
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

        $this->addFlash("success", "Eintrag approved");
        return $this->redirectToRoute('app_transaction_view', ['transaction' => $transaction->getId()]);

    }

    #[Route('/transaction/{transaction}/delete', name: 'app_transaction_delete')]
    public function delete(
        Transaction $transaction,
        Request $request,
        TransactionService $transactionService,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Security $security,
        ScoreService $scoreService,
    ): Response
    {

        if(!$transactionService->canEdit($this->getUser(), $transaction)) {
            throw new \Exception("Kei Recht du arsch");
        }

        $transaction->setActive(0);

        $entityManager->persist($transaction);
        $entityManager->flush();

        $this->addFlash("success", "Eintrag gelöscht");
        return $this->redirectToRoute('app_dashboard');

    }
}
