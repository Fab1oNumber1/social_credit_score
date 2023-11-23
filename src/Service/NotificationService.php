<?php

namespace App\Service;


use App\Entity\Notification;
use App\Entity\Transaction;
use App\Entity\TransactionComment;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Event\SentMessageEvent;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\Button\InlineKeyboardButton;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\InlineKeyboardMarkup;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Event\FailedMessageEvent;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationRepository $notificationRepository,
        private string $telegramApiUrl,
        private HttpClientInterface $httpClient,
        private UrlGeneratorInterface $urlGenerator,
        private string $siteUrl,
    )
    {
    }

    public function notify(string $message, Transaction|TransactionComment $obj, User $user)
    {

        $notification = new Notification();

        $notification->setMessage($message);
        $notification->setAuthor($user);
        $transaction_id = 0;
        $telegramMessage = $notification->getMessage() . PHP_EOL;
        switch (ClassUtils::getRealClass($obj::class)) {
            case Transaction::class:
                $notification->setType('transaction');
                $notification->setTransaction($obj);
                $transaction_id = $obj->getId();
                $telegramMessage .= '---> ' . $obj->getUser() .' ('. $obj->getValue() . ' Pünkt)' . PHP_EOL;
                $telegramMessage .= $obj->getDescription() . PHP_EOL;


                break;
            case TransactionComment::class:
                $notification->setType('transaction_comment');
                $notification->setTransactionComment($obj);
                $transaction_id = $obj->getTransaction()->getId();
                $telegramMessage .= '"'. $obj->getText() . '"' . PHP_EOL;
                $telegramMessage .= PHP_EOL;
                $telegramMessage .= '---> ' . $obj->getTransaction()->getUser() .  ' ('. $obj->getTransaction()->getValue() . ' Pünkt)' . PHP_EOL;
                $telegramMessage .= '"'.$obj->getTransaction()->getDescription().'"' . PHP_EOL;
                break;
            default:
                $notification->setType('');
        }
        $this->entityManager->persist($notification);
        $this->entityManager->flush();


        $url =
            $this->siteUrl . $this->urlGenerator->generate('app_transaction_view', ['transaction' => $transaction_id]);
        //$telegramMessage .= $url;
        $telegramOptions = (new TelegramOptions())
            ->parseMode('MarkdownV2')
            ->disableWebPagePreview(false)
            ->disableNotification(false)
            ->replyMarkup((new InlineKeyboardMarkup())
                ->inlineKeyboard([
                    (new InlineKeyboardButton('Kuck dir das an!'))
                        ->url($url),
                ])
            );

        $sendPhoto = false;
        if($notification->getType() === 'transaction' && $notification->getTransaction()->getMedia()) {
            //$telegramOptions->photo('https://afi-credit-score.jimsoft.ch/media/6');
            $sendPhoto = true;
            $telegramOptions->photo($this->siteUrl . $this->urlGenerator->generate('app_media', ['media' =>$notification->getTransaction()->getMedia()->getId()]));

        }
        $options = $telegramOptions?->toArray() ?? [];
        $options['text'] = $telegramMessage;

        if (!isset($options['parse_mode']) || TelegramOptions::PARSE_MODE_MARKDOWN_V2 === $options['parse_mode']) {
            $options['parse_mode'] = TelegramOptions::PARSE_MODE_MARKDOWN_V2;
            $options['text'] = preg_replace('/([_*\[\]()~`>#+\-=|{}.!\\\\])/', '\\\\$1', $telegramMessage);
        }

        if (isset($options['photo'])) {
            $options['caption'] = $options['text'];
            unset($options['text']);
        }

        $apiUrl = $this->telegramApiUrl;
        if($sendPhoto) {
            $apiUrl = str_replace('sendMessage', 'sendPhoto', $apiUrl);
        }

        $response = $this->httpClient->request('POST', $apiUrl, [
            'json' => array_filter($options),
        ]);

        return $notification;
    }

    public function getLatest()
    {
        return $this->notificationRepository->findBy([], ['created' => 'DESC'], 40);
    }
}