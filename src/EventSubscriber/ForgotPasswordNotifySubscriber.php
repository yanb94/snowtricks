<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ForgotPasswordNotifySubscriber implements EventSubscriberInterface
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
            Events::FORGOT_PASSWORD => 'onUserRequestPassword',
        ];
    }

    public function onUserRequestPassword(GenericEvent $event): void
    {
        /** @var User $user */
        $user = $event->getSubject();

        $subject = 'Réinitialisation du mot de passe';
        $body = 'Pour rénitialisé votre mot de passe cliqué sur le lien suivant : '.
        $this->router->generate(
            'reset_password',
            ['token' => $user->getResetToken()],
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
