<?php

namespace service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Common\Collections\Collection;
use model\Teacher;
use repository\TeacherRepository;

require_once 'src/repository/TeacherRepository.php';
class TeacherService
{
    private $courseRepo;

    public function __construct(TeacherRepository $commentRepo)
    {
        $this->courseRepo = $commentRepo;
    }

    public function createTeacher($username, $email, $password): Teacher
    {
        return $this->courseRepo->create($username, $email, $password);
    }

    public function deleteTeacher(Teacher $teacher): void
    {
        $this->courseRepo->delete($teacher);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findById($id): ?Teacher
    {
        return $this->courseRepo->findById($id);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByUsername($name): ?Teacher
    {
        return $this->courseRepo->findByUsername($name);
    }

    public function findAll(): array
    {
        return $this->courseRepo->findAll();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getCourses($id): Collection
    {
        return $this->findById($id)->getLeadingCourses();
    }

    public function update(Collection $courses, $id): ?Teacher
    {
        return $this->courseRepo->update($id, $courses);
    }
}