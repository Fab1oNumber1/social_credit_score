<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;

class TransactionService {

    public function canEdit(User $user, Transaction $transaction):bool {
        if($transaction->getAuthor()->getId() !== $user->getId()) {
            return false;
        }
        if($transaction->getStatus() !== 'review') {
            return false;
        }
        return true;
    }
}