<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\Security\TokenGenerator;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

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
            [User::ROLE_COMMENTATOR],
            [User::ROLE_ADMIN],
            [User::ROLE_EDITOR],
            [User::ROLE_SUPERADMIN],
            [User::ROLE_WRITER],
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
                ->setPassword($faker->password);
            if (!$author->isEnabled()) {
                $author->setConfirmationToken($this->generator->getRandomSecureToken());
            }
            $manager->persist($author);
            $users[] = $author;
        }

        /**
         * * create 20 posts
         */
        for ($i = 0; $i < 20; $i++) {
            $blogPost = new BlogPost();
            $content = '<p>' . join($faker->paragraphs(4), '<p></p>') . '</p>';
            $blogPost->setPublished($faker->dateTimeBetween('-6 months'))
                ->setContent($content)
                ->setTitle($faker->sentence($nbWords = 3, $variableNbWords = true))
                ->setSlug($slugify->slugify($blogPost->getTitle()))
                ->setAuthor($faker->randomElement($users));

            /**
             * * try to get dates values as real as possible
             */
            $Comment_content = $faker->sentence($nbWords = 6, $variableNbWords = true);
            $now = new DateTime();
            $interval = $now->diff($blogPost->getPublished());
            $days = $interval->days;

            /**
             * * set to every post a number of comments between 4 and 10
             */
            for ($k = 1; $k <= mt_rand(4, 10); $k++) {
                $comment = new Comment();
                $comment->setAuthor($faker->randomElement($users))
                    ->setContent($Comment_content)
                    ->setPublished($faker->dateTimeBetween('-' . $days . 'days'))
                    ->setPost($blogPost);
                $manager->persist($comment);
            }
            $manager->persist($blogPost);
        }
        $manager->flush();
    }
}
