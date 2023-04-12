<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
#[ORM\Table(name: "app_comments")]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column]
    private ?string $comments;

    #[Pure] public function __construct($comments)
    {
        $this->comments = $comments;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}