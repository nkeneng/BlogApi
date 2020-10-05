<?php


namespace App\Mailer;


use App\Entity\User;
use Twig\Environment;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twig->render(
            'email/confirmation.html.twig',
            [
                'user' => $user,
            ]
        );


        $message = (new \Swift_Message('Please confirm your account'))
            ->setFrom('steven.dave918@gmail.com')
            ->setTo('ganarag210@armcams.com')
            ->setBody($body,'text/html');

        $this->mailer->send($message);
    }
}
