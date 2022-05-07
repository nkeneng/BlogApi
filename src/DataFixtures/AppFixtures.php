<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Security\TokenGenerator;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{


    /**
     * @var TokenGenerator
     */
    private $generator;

    public function __construct(TokenGenerator $generator)
    {

        $this->generator = $generator;
    }


    public function load(ObjectManager $manager)
    {
        $slugify = new Slugify();
        $faker = \Faker\Factory::create('fr_FR');
        $users = [];
        $roles = [
            [User::ROLE_SUPERADMIN],
            [User::ROLE_USER],
        ];

        /**
         * * create 10 users
         */
        for ($k = 0; $k < 10; $k++) {
            $author = new User();
            $author->setEmail($faker->email)
                ->setName($faker->name)
                ->setUsername($faker->userName)
                ->setRoles($roles[array_rand($roles, 1)])
                ->setEnabled($faker->boolean)
                ->setPassword('admin');
            if (!$author->isEnabled()) {
                $author->setConfirmationToken($this->generator->getRandomSecureToken());
            }
            $manager->persist($author);
            $users[] = $author;
        }

        $manager->flush();
    }
}
