<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\Interfaces\AuthoredEntityInterface;
use App\Entity\Interfaces\PublishedDateEntityInterface;
use App\Repository\BlogPostRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * * itemOperation tells which verbs are allow by
 * fetching one item
 * * collectionOperations tells which verbs are
 * allow when fetching all items
 *
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *     "id"= "exact",
 *     "title" = "partial",
 *     "content"= "partial"
 *     }
 * )
 * @ApiResource(
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"blog"}
 *          }
 *     },
 *          "put"={
 *              "access_control"="is_granted('ROLE_WRITER') or is_granted('ROLE_EDITOR') and object.getAuthor() === user"
 *              }
 *          },
 *     collectionOperations={
 *          "get",
 *          "post"={
 *              "access_control"="is_granted('ROLE_WRITER')"
 *             }
 *          },
 *      denormalizationContext={
 *          "groups"={
 *              "post"
 *              }
 *           }
 *      )
 */
class BlogPost implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"blog"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"post","blog"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"blog"})
     */
    private $published;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Groups({"post","blog"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Groups({"post","blog"})
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blogPosts")
     * @Groups({"blog"})
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post")
     * @Groups({"blog"})
     * @ApiSubresource()
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=Image::class,cascade={"persist"})
     *
     * @Groups({"post","blog"})
     * @ApiSubresource()
     */
    private $images;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->published = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPublished(): ?DateTimeInterface
    {
        return $this->published;
    }

    /**
     * @ORM\PrePersist
     */
    public function setPublishedValue()
    {
        $this->published = new \DateTime();
    }

    public function setPublished(DateTimeInterface $published): PublishedDateEntityInterface
    {
        $this->published = $published;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     * @return BlogPost
     */
    public function setSlug($slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }
        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }
        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}
