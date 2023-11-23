<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationService $notificationService,
    )
    {
    }

    public function canEdit(User $user, Transaction $transaction):bool {
        if($transaction->getAuthor()->getId() !== $user->getId()) {
            return false;
        }
        if($transaction->getStatus() !== 'review') {
            return false;
        }
        return true;
    }

    public function getTimeUntilArchive():\DateTimeImmutable {
        return new \DateTimeImmutable('-2 days');
    }

    public function shouldArchive(Transaction $transaction):bool {
        if($transaction->getStatus() !== 'review') {
            return false;
        }
        if($transaction->getCreated() >= $this->getTimeUntilArchive()) {
            return false;
        }
        return true;
    }
    public function archive(Transaction $transaction){
        $transaction->setStatus('archived');
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
        $user = $transaction->getAuthor();
        // fallback user, should not happen in production
        if(!$user) {
            $user = $this->entityManager->find(User::class, 1);
        }
        $this->notificationService->notify("Eintrag wurde archiviert", $transaction, $user);

    }
}