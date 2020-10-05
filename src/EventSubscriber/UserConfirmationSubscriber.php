<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\UserConfirmationToken;
use App\Security\UserConfirmationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;


class UserConfirmationSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserConfirmationService
     */
    private $confirmationService;

    public function __construct(UserConfirmationService $confirmationService)
    {
        $this->confirmationService = $confirmationService;
    }

    public function onKernelView(ViewEvent $event)
    {
       $request = $event->getRequest();
       if ('api_user_confirmation_tokens_post_collection' !== $request->get('_route')){
           return;
       }

       /** @var UserConfirmationToken $confirmation */
       $confirmation = $event->getControllerResult();
       $this->confirmationService->confirmUser($confirmation->confirmationToken);

      $event->setResponse(new JsonResponse(null,Response::HTTP_OK));
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.view' => ['onKernelView',EventPriorities::POST_VALIDATE],
        ];
    }
}
