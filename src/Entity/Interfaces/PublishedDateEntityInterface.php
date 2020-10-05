<?php


namespace App\Entity\Interfaces;


interface PublishedDateEntityInterface
{
    public function setPublished(\DateTimeInterface $published): ?PublishedDateEntityInterface;
}
