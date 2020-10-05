<?php


namespace App\Security;


use App\Entity\User;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserEnabledChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User){
            return;
        }
        // * if the user is not enabled
        if (!$user->isEnabled()){
            throw new DisabledException();
        }
    }

    public function checkPostAuth(UserInterface $user)
    {

    }
}
