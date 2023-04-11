<?php

namespace model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;
use repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "app_user")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(unique: true)]
    private ?string $username;

    #[ORM\Column(unique: true)]
    private ?string $email;

    #[ORM\Column]
    private ?string $password;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comments::class)]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Lecture::class, mappedBy: 'user')]
    private Collection $favoriteCourses;

    #[ORM\ManyToMany(targetEntity: Lecture::class, mappedBy: 'user')]
    private Collection $ownCourses;

    #[Pure] public function __construct($username, $email, $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->reviews = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->favoriteCourses = new ArrayCollection();
        $this->ownCourses = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getOwnCourses(): Collection
    {
        return $this->ownCourses;
    }

    public function getReview(): Collection
    {
        return $this->reviews;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getFavoriteCourses(): Collection
    {
        return $this->favoriteCourses;
    }

    public function setOwnCourses(Collection $courses): self
    {
        $this->ownCourses = $courses;
        return $this;
    }

    public function setFavouriteCourses(Collection $courses): self
    {
        $this->favoriteCourses = $courses;
        return $this;
    }

    public function setReviews(Collection $courses): self
    {
        $this->reviews = $courses;
        return $this;
    }

    public function setComments(Collection $courses): self
    {
        $this->comments = $courses;
        return $this;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}