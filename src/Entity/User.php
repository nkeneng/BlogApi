<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\ResetPasswordAction;


/**
 * * restricted access to certain object (
 * access_control )
 * * like here only authenticated users can
 * access
 * * but every body can access thinks like
 * comments or posts
 * @ApiResource(
 *     itemOperations={
 *         "get"={
 *          "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *          "normalization_context"={
 *              "groups"={"get"}
 *         }
 *    },
 *     "put"={
 *          "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object === user",
 *          "denormalization_context"={
 *                 "groups"={"put"}
 *                }
 *          },
 *   "put_reset_password"={
 *          "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object === user",
 *          "method"= "PUT",
 *          "path"="/users/{id}/reset-password",
 *           "controller"=ResetPasswordAction::class,
 *           "denormalization_context"={
 *                 "groups"={"put_reset_password"}
 *                },
 *     "validation_groups"={"put_reset_password"}
 *              }
 *          },
 *   collectionOperations={
 *         "post"={
 *              "denormalization_context"={
 *                   "groups"={"post"}
 *                  },
 *              "validation_groups"={"post"}
 *              }
 *          },
 *      )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username","email"})
 */
class User implements UserInterface
{
    const ROLE_COMMENTATOR = 'ROLE_COMMENTATOR';
    const ROLE_WRITER = 'ROLE_WRITER';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';
    const DEFAULT_ROLES = [self::ROLE_COMMENTATOR];


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * * not possible for the user to post an id
     *     because that is done directly by
     *     doctrine
     * @Groups({"get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(groups={"post"})
     * * not possible for the user to change his
     *  *   username
     * @Groups({"get","post","comment","blog"})
     */
    private $username;

    /**
     * @Groups({"post"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getConfirmPassword()",
     *     message="Passwords don't match",
     *     groups={"post"}
     * )
     */
    private $confirmPassword;

    /**
     * @Groups({"post"})
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven
     *     characters long ans contain at least
     *     one digit,one uppercase and one lower
     *     case letter", groups={"post"}
     * )
     *
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     * @Assert\NotBlank(groups={"post","put"})
     * @Groups({"get","put","post"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(groups={"post","put"})
     * @Assert\Email(groups={"post","put"})
     * @Groups({"put","post","admin_readable","user_readable"})
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=BlogPost::class, mappedBy="author")
     * @Groups({"get"})
     */
    private $blogPosts;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author")
     * @Groups({"get"})
     */
    private $comments;

    /**
     * @ORM\Column(type="simple_array", length=200)
     * @Groups({"admin_readable","user_readable"})
     */
    private $roles;

    /**
     * @var
     * @Groups({"put_reset_password"})
     * @Assert\NotBlank(groups={"put_reset_password"})
     * @UserPassword(groups={"put_reset_password"})
     */
    private $oldPassword;

    /**
     * @var
     * @Groups({"put_reset_password"})
     * @Assert\NotBlank(groups={"put_reset_password"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven
     *     characters long ans contain at least
     *     one digit,one uppercase and one lower
     *     case letter",
     *     groups={"put_reset_password"}
     * )
     */
    private $newPassword;

    /**
     * @var
     * @Groups({"put_reset_password"})
     * @Assert\NotBlank(groups={"put_reset_password"})
     * @Assert\Expression(
     *     "this.getNewPassword() ===
     *     this.getNewPasswordConfirm()",
     *     message="Passwords don't match",
     *     groups={"put_reset_password"}
     * )
     */
    private $newPasswordConfirm;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $confirmationToken;


    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
        $this->enabled = false;
        $this->passwordChangeDate = null;
        $this->confirmationToken = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|BlogPost[]
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts[] = $blogPost;
            $blogPost->setAuthor($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->contains($blogPost)) {
            $this->blogPosts->removeElement($blogPost);
            // set the owning side to null (unless algety changed)
            if ($blogPost->getAuthor() === $this) {
                $blogPost->setAuthor(null);
            }
        }

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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless algety changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * @param mixed $confirmPassword
     */
    public function setConfirmPassword($confirmPassword): void
    {
        $this->confirmPassword = $confirmPassword;
    }

    /**
     * @return mixed
     */
    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    /**
     * @param mixed $oldPassword
     */
    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return mixed
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @param mixed $newPassword
     */
    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return mixed
     */
    public function getNewPasswordConfirm(): ?string
    {
        return $this->newPasswordConfirm;
    }

    /**
     * @param mixed $newPasswordConfirm
     */
    public function setNewPasswordConfirm($newPasswordConfirm): void
    {
        $this->newPasswordConfirm = $newPasswordConfirm;
    }

    public function getPasswordChangeDate(): ?int
    {
        return $this->passwordChangeDate;
    }

    public function setPasswordChangeDate(int $passwordChangeDate): self
    {
        $this->passwordChangeDate = $passwordChangeDate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return User
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return null
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param null $confirmationToken
     */
    public function setConfirmationToken($confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function __toString()
    {
        return $this->name;
    }
}
