<?php


namespace App\Security;

use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UserConfirmationService
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(EntityManagerInterface $manager,
                                UserRepository $repository,
                                LoggerInterface $logger
    )
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    public function confirmUser(string $confirmationToken)
    {
        $this->logger->debug('fetching user by confirmation token');
        $user = $this->repository->findOneBy(['confirmationToken' => $confirmationToken]);

        if (!$user){
            $this->logger->debug('User by confirmation token not found');
            throw new InvalidConfirmationTokenException();
        }
        $user->setEnabled(true)
            ->setConfirmationToken(null);
        $this->manager->flush();
        $this->logger->debug('confirm user by token');
    }
}
