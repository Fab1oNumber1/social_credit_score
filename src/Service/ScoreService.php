<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;

class ScoreService {
    public function __construct(
        private NotificationService $notificationService
    )
    {
    }


    public function calculate(User $user):int {
        $total = 0;
        foreach ($user->getTransactions() as $transaction) {
            if($transaction->getStatus() === 'approved') {
                $total += $transaction->getValue();
            }
        }
        return $total;
    }


    public function numberOfNeededApprovments():int {
        return 3;
    }

    public function canApprove(User $user, Transaction $transaction) {
        foreach ($transaction->getApprovers() as $approver) {
            if($approver->getId() === $user->getId()) {
                return false;
            }
        }
        return true;
    }

    public function approve(User $user, Transaction $transaction): Transaction{
        $transaction->addApprover($user);
        if( count($transaction->getApprovers()) >= $this->numberOfNeededApprovments()) {
            $transaction->setStatus('approved');

        }
        return $transaction;
    }





}