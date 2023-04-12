<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
#[ORM\Table(name: "app_teacher")]
class Teacher
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

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Lecture::class)]
    private Collection $courses;

    #[Pure] public function __construct($username, $email, $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->courses = new ArrayCollection();
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

    public function getLeadingCourses(): Collection
    {
        return $this->courses;
    }

    public function addNewCourse(Course $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
        }

        return $this;
    }

    public function setCourses(Collection $courses): self
    {
        $this->courses = $courses;
        return $this;
    }

    public function removeCourse(Course $course): self
    {
        if ($this->courses->contains($course)) {
            $course->getComments()->clear();
            $course->getReviews()->clear();
            $course->getUsers()->clear();
            $course->getLectures()->clear();
            $this->courses->removeElement($course);
        }

        return $this;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}