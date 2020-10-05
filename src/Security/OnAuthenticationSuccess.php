<?php


namespace App\Security;


use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\Security\Core\User\UserInterface;

class OnAuthenticationSuccess extends AuthenticationSuccessHandler
{
    public function handleAuthenticationSuccess(UserInterface $user, $jwt = null)
    {
        $response = parent::handleAuthenticationSuccess($user, $jwt);
        $newData = [
          'token'=>  json_decode($response->getContent(),true)['token'],
           'id' => $id = $user->getId()
        ];
        $response->setData($newData);
        return $response;
    }
}
