<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ImageRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Controller\UploadImageAction;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @Vich\Uploadable()
 *
 *@ApiResource(
 *     collectionOperations={
 *         "post"={
 *              "normalization_context"={
 *                  "groups"={"image"}
 *          },
 *              "method" = "POST",
 *              "path" = "/images",
 *              "controller" = UploadImageAction::class,
 *              "deserialize"=false,
 *              "defaults" = {"api_receive"=false},
 *             "validation_groups"={"Default", "media_object_create"},
 *             "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *         },
 *         "get"
 *     },
 * )
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"image"})
     */
    private $id;

    /**
     *@var string|null
     * @ORM\Column(type="string", length=255)
     * @Groups({"image","blog"})
     */
    private $url;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="images" , fileNameProperty= "url")
     * @Assert\NotNull(groups={"media_object_create"})
     *
     */
    public $file;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     * @var DateTime|null
     */
    private $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $imageFile
     */
    public function setFile(?File $imageFile = null): void
    {
        $this->file = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }


    public function __toString()
    {
       return $this->id.':'.$this->url;
    }
}
