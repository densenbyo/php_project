<?php

namespace App\Service;

use App\Entity\Teacher;
use App\Repository\TeacherRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Common\Collections\Collection;

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

    /**
     * @throws NonUniqueResultException
     */
    public function update(Collection $courses, $id): ?Teacher
    {
        return $this->courseRepo->update($id, $courses);
    }
}