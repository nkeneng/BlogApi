<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BlogController extends AbstractController
{
//    private const POSTS = [
//        [
//            'id' => 1,
//            'slug' => 'hello-world',
//            'title' => 'hello world'
//        ],
//        [
//            'id' => 2,
//            'slug' => 'another-example',
//            'title' => 'Another example'
//        ],
//        [
//            'id' => 3,
//            'slug' => 'last-example',
//            'title' => 'last example'
//        ]
//    ];

    /**
     * @var BlogPostRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(BlogPostRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BlogController.php',
        ]);
    }

    /**
     * @Route("/blog/list",name="blog_list",methods={"GET"})
     */
    public function list()
    {
//        return $this->json(self::POSTS);
        return $this->json($this->repository->findAll());
    }

    /**
     * to specify which type of variable should
     * be pass as variable
     * @Route("/blog/{id}",name="blog_by_id",requirements={"id"="\d+"},methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function post($id)
    {
//        return $this->json(self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]);
        return $this->json($this->repository->find($id));
    }

    /**
     * @Route("/blog/{slug}",name="blog_by_slug",methods={"GET"})
     * @param $slug
     * @return JsonResponse
     */
    public function postBySlug($slug)
    {
//        return $this->json(self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]);
        return $this->json($this->repository->findOneBy(['slug' => $slug]));
    }

    /**
     * @Route("/add",name="blog_add",methods={"POST"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function add(Request $request, SerializerInterface $serializer)
    {
        /** @var BlogPost $blogPost */
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $blogPost->setPublished(new \DateTime());
        $this->manager->persist($blogPost);
        $this->manager->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/blog/{id}",name="blog_delete",methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $this->manager->remove($this->repository->find($id));
        $this->manager->flush();
        return new JsonResponse('deleted', Response::HTTP_NO_CONTENT);
    }
}
