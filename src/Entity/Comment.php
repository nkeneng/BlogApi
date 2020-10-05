<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Interfaces\AuthoredEntityInterface;
use App\Entity\Interfaces\PublishedDateEntityInterface;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "put"={
 *              "access_control"="(is_granted('ROLE_COMMENTATOR') or is_granted('ROLE_EDITOR'))
 *     and object.getAuthor() === user"
 *          }
 *     },
 *     collectionOperations={
 *           "get",
 *          "post"={
 *              "access_control"="is_granted('ROLE_COMMENTATOR')"
 *          },
 *
 * },
 *
 *  subresourceOperations={
 *      "api_blog_posts_comments_get_subresource"={
 *          "method"="GET",
 *          "normalization_context"={
 *              "groups"={"comment"}
 *          }
 *      }
 * },
 *   denormalizationContext={
 *          "groups"={
 *              "post"
 *              }
 *        }
 * )
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post","comment","blog"})
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"comment"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity=BlogPost::class, inversedBy="comments")
     * @Groups({"post"})
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @Groups({"comment"})
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublishedDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }

    public function getPost(): ?BlogPost
    {
        return $this->post;
    }

    public function setPost(?BlogPost $post): self
    {
        $this->post = $post;

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
    public function __toString()
    {
        return substr($this->content,0,20).'...';
    }
}
