<?php

namespace model;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use repository\LectureRepository;

#[ORM\Entity(repositoryClass: LectureRepository::class)]
#[ORM\Table(name: "app_lecture")]
class Lecture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(unique: true)]
    private ?string $name;

    #[ORM\Column]
    private ?string $content;

    #[Pure] public function __construct($name, $content)
    {
        $this->name = $name;
        $this->content = $content;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $topic): self
    {
        $this->content = $topic;

        return $this;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}