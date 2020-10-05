<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserConfirmationToken
 * @package App\Entity
 *
 * @ApiResource(
 *     collectionOperations={
 *     "post"={
 *     "path"="/users/confirm"
 *          }
 *     },
 *     itemOperations={}
 * )
 */
class UserConfirmationToken
{
    /**
     * @Assert\NotBlank()
     *
     */
    public $confirmationToken;
}
