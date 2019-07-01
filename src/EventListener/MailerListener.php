<?php

namespace App\EventListener;

use App\Framework\Notify\Email\BaseEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;

class MailerListener implements EventSubscriberInterface
{
    /** @var string */
    protected $emailFrom;

    public function __construct(string $emailFrom)
    {
        $this->emailFrom = $emailFrom;
    }

    public function onMessageSend(MessageEvent $event)
    {
        $message = $event->getMessage();
        if (!$message instanceof BaseEmail) {
            return;
        }

        $message
            ->from($this->emailFrom)
            ->subject($message->getPredefinedSubject())
            ->htmlTemplate($message->getPredefinedTemplate())
            ->context($message->getContext() + [
                'subject' => $message->getPredefinedSubject(),
            ])
        ;
    }

    public static function getSubscribedEvents()
    {
        return [MessageEvent::class => 'onMessageSend'];
    }
}
