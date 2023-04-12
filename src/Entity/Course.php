<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ORM\Table(name: "app_course")]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(unique: true)]
    private ?string $name;

    #[ORM\Column]
    private ?int $price;

    #[ORM\Column]
    private ?string $topic;

    #[ORM\Column]
    private ?\DateTime $startingDate;

    #[ORM\Column]
    private ?string $details;

    #[ORM\Column]
    private ?int $lectureNum;

    #[ORM\ManyToMany(targetEntity: Comments::class, mappedBy: 'course')]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Review::class, mappedBy: 'course')]
    private Collection $reviews;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'course')]
    private Collection $users;

    #[ORM\ManyToMany(targetEntity: Lecture::class, mappedBy: 'course')]
    private Collection $lectures;

    #[Pure] public function __construct(string $name, string $details, string $topic, \DateTime $startingDate, int $price)
    {
        $this->name = $name;
        $this->details = $details;
        $this->topic = $topic;
        $this->startingDate = $startingDate;
        $this->price = $price;
        $this->lectureNum = 0;
        $this->reviews = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->lectures = new ArrayCollection();
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

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getLectureNum(): ?int
    {
        return $this->lectureNum;
    }

    public function setLectureNum(string $num): self
    {
        $this->lectureNum = $num;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startingDate;
    }

    public function setStartDate(\DateTime $date): self
    {
        $this->startingDate = $date;

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getLectures(): Collection
    {
        return $this->lectures;
    }

    public function setComments(Collection $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function setUsers(Collection $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function setReviews(Collection $reviews): self
    {
        $this->reviews = $reviews;

        return $this;
    }

    public function setLectures(Collection $lectures): self
    {
        $this->lectures = $lectures;

        return $this;
    }

    public function addComments(Comments $comments): self
    {
        if (!$this->comments->contains($comments)) {
            $this->comments->add($comments);
        }

        return $this;
    }

    public function removeComments(Comments $comments): self
    {
        if ($this->comments->contains($comments)) {
            $this->comments->removeElement($comments);
        }

        return $this;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
        }

        return $this;
    }

    public function addUserToCourse(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUserFromCourse(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    public function addLecture(Lecture $lecture): self
    {
        if (!$this->lectures->contains($lecture)) {
            $this->lectures->add($lecture);
            $num = $this->getLectureNum();
            $this->setLectureNum($num + 1);
        }

        return $this;
    }

    public function removeLecture(Lecture $lecture): self
    {
        if ($this->lectures->contains($lecture)) {
            $this->lectures->removeElement($lecture);
            $num = $this->getLectureNum();
            $this->setLectureNum($num - 1);
        }

        return $this;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}