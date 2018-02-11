<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationNotifySubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $sender;
    private $router;

    public function __construct(\Swift_Mailer $mailer, $sender, $router)
    {
        $this->mailer = $mailer;
        $this->sender = $sender;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::USER_REGISTERED => 'onUserRegistrated',
        ];
    }

    public function onUserRegistrated(GenericEvent $event): void
    {
        /** @var User $user */
        $user = $event->getSubject();

        $subject = 'Confirmer votre inscription';
        $body = 'Votre inscription à bien été prise en compte'.
        ' mais vous devez encore la confirmer en cliquant sur le lien suivant : '.
        $this->router->generate(
            'confirm_user',
            ['token' => $user->getValidationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setTo($user->getEmail())
            ->setFrom($this->sender)
            ->setBody($body, 'text/html')
        ;

        $this->mailer->send($message);
    }
}
