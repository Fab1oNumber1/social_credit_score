<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Transaction;
use App\Entity\TransactionComment;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    #[Route('/transactions', name: 'transactions', methods: ['GET'])]
    public function transactions(
        Request $request,
        TransactionRepository $transactionRepository,
    ): Response
    {
        $result = [];

        $filters = $request->get('filters', []);


        foreach ($transactionRepository->findBy($filters) as $transaction) {
            $result[] = $this->transactionToArray($transaction);
        }
        return $this->json($result);
    }


    #[Route('/notifications', name: 'notifications', methods: ['GET'])]
    public function notifications(
        Request $request,
        TransactionRepository $transactionRepository,
        NotificationRepository $notificationRepository,
    ): Response
    {
        $result = [];

        $filters = $request->get('filters', []);
        $filters['active'] = 1;


        foreach ($notificationRepository->findBy($filters, ['created' => 'DESC'], 50) as $notification) {
            $result[] = $this->notificationToArray($notification);
        }
        return $this->json($result);
    }

    private function notificationToArray(?Notification $notification) {
        if(!$notification) {
            return null;
        }
        return [

            'id' => $notification->getId(),
            'created' => $notification->getCreated()->format('Y-m-d H:i:s'),
            'updated' => $notification->getUpdated()->format('Y-m-d H:i:s'),
            'author' =>$this->userToArray($notification->getAuthor()),
            'message' => $notification->getMessage(),
            'type' => $notification->getType(),
            'transaction' => $this->transactionToArray($notification->getTransaction()),
            'transactionComment' => $this->commentToArray($notification->getTransactionComment()),
        ];
    }

    private function transactionToArray(?Transaction $transaction) {
        if(!$transaction) {
            return null;
        }
        return [
            'id' => $transaction->getId(),
            'created' => $transaction->getCreated()->format('Y-m-d H:i:s'),
            'updated' => $transaction->getUpdated()->format('Y-m-d H:i:s'),
            'author' =>$this->userToArray($transaction->getAuthor()),
            'user' =>$this->userToArray($transaction->getUser()),
            'value' => $transaction->getValue(),
            'status' => $transaction->getStatus(),
            'approvers' => array_map(fn($approver) => $this->userToArray($approver), $transaction->getApprovers()->toArray()),
            'description' => $transaction->getDescription(),
            'comments' => array_map(fn($c) => $this->commentToArray($c), $transaction->getTransactionComments()->toArray()),
        ];
    }
    private function commentToArray(?TransactionComment $comment) {
        if(!$comment) {
            return null;
        }
        return [
            'id' => $comment->getId(),
            'text' => $comment->getText(),
            'author' => $this->userToArray($comment->getAuthor()),
            'created' => $comment->getCreated()->format('Y-m-d H:i:s'),
            'updated' => $comment->getUpdated()->format('Y-m-d H:i:s'),
        ];

    }
    private function userToArray(?User $user)
    {
        if(!$user) {
            return null;
        }
        return [
            'id' => $user->getId(),
            'firstName' => $user->getFirstname(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];
    }
}
