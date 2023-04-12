<?php

namespace App\Service;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;


class CourseService
{
    private $courseRepo;

    public function __construct(CourseRepository $commentRepo)
    {
        $this->courseRepo = $commentRepo;
    }

    public function getAll(): array
    {
        return $this->courseRepo->findAll();
    }

    public function createCourse(string $name, string $details, string $topic, string $startingDate, string $price): Course
    {
        $date = new \DateTime($startingDate);
        return $this->courseRepo->create($name, $details, $topic, $date, $price);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function deleteCourse(int $id): void
    {
        // Add any validation or business logic here

        $course = $this->courseRepo->findById($id);
        $this->courseRepo->delete($course);
    }

    public function findByName(string $name): ?Course
    {
        return $this->courseRepo->findCourseByName($name);
    }

    public function findByTopic(string $name): array
    {
        return $this->courseRepo->findCoursesByTopic($name);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function updateCourse(int $id, array $data): ?Course
    {
        return $this->courseRepo->update($data, $id);
    }
}