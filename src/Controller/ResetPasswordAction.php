<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordAction
{

    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var JWTTokenManagerInterface
     */
    private $tokenManager;

    public function __construct(ValidatorInterface $validator,
                                UserPasswordEncoderInterface $encoder,
                                EntityManagerInterface $manager,
                                JWTTokenManagerInterface $tokenManager
    )
    {

        $this->validator = $validator;
        $this->encoder = $encoder;
        $this->manager = $manager;
        $this->tokenManager = $tokenManager;
    }

    /**
     * * this method will be called every time an
     * instance of this class will be created
     * @param User $data
     */
    public function __invoke(User $data)
    {
        $this->validator->validate($data);

        $data->setPassword(
            $this->encoder->encodePassword(
                $data, $data->getNewPassword()
            )
        );

        // ! after changing password , the old token is still valid , so we need to invalidate it
        // * time() represent the time when that users password was changed
        $data->setPasswordChangeDate(time());

        // ! the validation is called only when we return the data from this action

        // ! the problem is that doctrine looks for user current password but we changed it on top

        // ? one solution should be to persist the data our self

        // * we don't need to persist again because the user already exist and doctrine is smart enough to understand that
        $this->manager->flush();

        // * create a new token for the user so that he doesn't need to logout
        $token = $this->tokenManager->create($data);

        return new JsonResponse(['token' => $token]);

        // ! entity is persisted automatically only if validation poss


    }
}
