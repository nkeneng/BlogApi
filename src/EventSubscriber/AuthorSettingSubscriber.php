<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Interfaces\AuthoredEntityInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthorSettingSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelView(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $author = $this->tokenStorage->getToken()->getUser();

        if (!$entity instanceof AuthoredEntityInterface || Request::METHOD_POST !== $method) {
            return;
        }
        if ($author instanceof User){
            $entity->setAuthor($author);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.view' => ['onKernelView',EventPriorities::PRE_WRITE]
        ];
    }
}
