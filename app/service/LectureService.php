<?php

namespace service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\Lecture;
use repository\LectureRepository;

require_once 'src/repository/LectureRepository.php';
class LectureService
{
    private $courseRepo;

    public function __construct(LectureRepository $commentRepo)
    {
        $this->courseRepo = $commentRepo;
    }

    public function createLecture($name, $content): Lecture
    {
        return $this->courseRepo->create($name, $content);
    }

    public function deleteLecture(Lecture $lecture): void
    {
        $this->courseRepo->delete($lecture);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findById($id): ?Lecture
    {
        return $this->courseRepo->findById($id);
    }

    public function findAll(): array
    {
        return $this->courseRepo->findAll();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByName($name): ?Lecture
    {
        return $this->courseRepo->findLectureByName($name);
    }
}