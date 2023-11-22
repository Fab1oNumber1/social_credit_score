<?php

namespace App\Service;


use App\Entity\Notification;
use App\Entity\Transaction;
use App\Entity\TransactionComment;
use App\Repository\NotificationRepository;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationRepository $notificationRepository,
    )
    {
    }

    public function notify(string $message, Transaction|TransactionComment $obj)
    {
        $notification = new Notification();

        $notification->setMessage($message);
        $notification->setAuthor($obj->getAuthor());
        switch (ClassUtils::getRealClass($obj::class)) {
            case Transaction::class:
                $notification->setType('transaction');
                $notification->setTransaction($obj);
                break;
            case TransactionComment::class:
                $notification->setType('transaction_comment');
                $notification->setTransactionComment($obj);
                break;
            default:
                $notification->setType('');
        }
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        return $notification;
    }

    public function getLatest()
    {
        return $this->notificationRepository->findBy([], ['created' => 'DESC'], 40);
    }
}