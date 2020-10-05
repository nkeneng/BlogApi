<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordHashSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => 'onUpdate',
            BeforeEntityPersistedEvent::class => 'onPersist'
        ];
    }

    public function onPersist(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof User) {
            return;
        }

        $this->passwordEncode($entity);
    }

    public function onUpdate(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof User) {
            return;
        }

        $this->passwordEncode($entity);
    }

    /**
     * @param User $entity
     */
    private function passwordEncode(User $entity): void
    {
        $entity->setPassword(
            $this->encoder->encodePassword($entity, $entity->getPassword()
            ));
    }
}
