<?php

namespace service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\Course;
use repository\CourseRepository;

require_once 'src/repository/CourseRepository.php';

class CourseService
{
    private $courseRepo;

    public function __construct(CourseRepository $commentRepo)
    {
        $this->courseRepo = $commentRepo;
    }

    public function createCourse($name, $details, $topic, $startingDate, $price): Course
    {
        return $this->courseRepo->create($name, $details, $topic, $startingDate, $price);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function deleteCourse($id): void
    {
        // Add any validation or business logic here

        $course = $this->courseRepo->findById($id);
        $this->courseRepo->delete($course);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByName($name): ?Course
    {
        return $this->courseRepo->findCourseByName($name);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByTopic($name): array
    {
        return $this->courseRepo->findCoursesByTopic($name);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function updateCourse($id, array $data): ?Course
    {
        return $this->courseRepo->update($data, $id);
    }
}