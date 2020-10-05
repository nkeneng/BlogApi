<?php


namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UploadImageAction
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(FormFactoryInterface $formFactory,EntityManagerInterface $manager , ValidatorInterface $validator)
    {
        $this->formFactory = $formFactory;
        $this->manager = $manager;
        $this->validator = $validator;
    }

    public function __invoke(Request $request)
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $image = new Image();
        $image->file = $uploadedFile;

        return $image;


//        // * create a new image instance
//       $image = new Image();
//
//       // * validate the form
//
//        $form = $this->formFactory->create(ImageType::class,$image);
//        $form->handleRequest($request);
//
//        var_dump($form);
//
//        if ($form->isSubmitted() && $form->isValid()){
//            // * persist the new image
//            $this->manager->persist($image);
//            $this->manager->flush();
//
//            return $image;
//        }
//
//        // * throw on validatiion exception
//        throw  new ValidationException(
//          $this->validator->validate($image)
//        );

    }
}
