<?php


namespace App\EventSubscriber;



use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class SetBlogPostUserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
       return [
           BeforeEntityPersistedEvent::class => 'onPersist'
       ];
    }

    public function onPersist(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if (!$entity instanceof  BlogPost){
            return;
        }
        $entity->setAuthor($this->security->getUser());
    }
}
