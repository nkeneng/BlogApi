<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\BlogPost;
use App\Entity\Interfaces\PublishedDateEntityInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class PublishedDateEntitySubscriber implements EventSubscriberInterface
{
    public function onKernelView(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$entity instanceof PublishedDateEntityInterface || Request::METHOD_POST !== $method) {
            return;
        }
        $entity->setPublished(new \DateTime());
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.view' => ['onKernelView',EventPriorities::PRE_WRITE],
        ];
    }
}
