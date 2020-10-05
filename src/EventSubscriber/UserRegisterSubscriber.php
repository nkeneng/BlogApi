<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Mailer\Mailer;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegisterSubscriber implements EventSubscriberInterface
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var TokenGenerator
     */
    private $generator;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(UserPasswordEncoderInterface $encoder,
                                TokenGenerator $generator,
                                Mailer $mailer
    )
    {
        $this->encoder = $encoder;
        $this->generator = $generator;
        $this->mailer = $mailer;
    }

    public function onKernelView(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || !in_array($method, [Request::METHOD_POST])) {
            return;
        }

        // * it is a user and we need to hash password
        $user->setPassword(
            $this->encoder->encodePassword($user, $user->getPassword())
        );

        // * set confirmation token

        $user->setConfirmationToken($this->generator->getRandomSecureToken());


        $this->mailer->sendConfirmationEmail($user);

    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.view' => ['onKernelView', EventPriorities::PRE_WRITE],
        ];
    }
}
