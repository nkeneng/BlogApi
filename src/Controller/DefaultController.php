<?php

namespace App\Controller;

use App\Security\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        return $this->render(
            'base.html.twig'
        );
    }

    /**
     * @Route("/confirm-user/{token}", name="confirm_user")
     * @param string $token
     * @param UserConfirmationService $confirmationService
     * @return RedirectResponse
     * @throws \App\Exception\InvalidConfirmationTokenException
     */
    public function confirmUser(string $token,UserConfirmationService $confirmationService)
    {
        $confirmationService->confirmUser($token);

        return $this->redirectToRoute('default');
    }
}
