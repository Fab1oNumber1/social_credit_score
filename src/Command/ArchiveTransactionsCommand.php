<?php

namespace App\Command;

use App\Repository\TransactionRepository;
use App\Service\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:archive-transactions',
    description: 'Add a short description for your command',
)]
class ArchiveTransactionsCommand extends Command
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private  EntityManagerInterface $entityManager,
        private TransactionService $transactionService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $transactions = $this->transactionRepository->findBy(['status' => 'review']);
        foreach($transactions as $transaction) {

            if($this->transactionService->shouldArchive($transaction)) {
                //dump($transaction->getCreated());
                $this->transactionService->archive($transaction);
            }

        }


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
