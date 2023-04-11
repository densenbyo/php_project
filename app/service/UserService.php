<?php

namespace service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Common\Collections\Collection;
use model\User;
use repository\UserRepository;

require_once 'src/repository/UserRepository.php';
class UserService
{
    private $courseRepo;

    public function __construct(UserRepository $commentRepo)
    {
        $this->courseRepo = $commentRepo;
    }

    public function createUser($username, $email, $password): User
    {
        return $this->courseRepo->create($username, $email, $password);
    }

    public function deleteUser(User $teacher): void
    {
        $this->courseRepo->delete($teacher);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findById($id): ?User
    {
        return $this->courseRepo->findById($id);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByUsername($name): ?User
    {
        return $this->courseRepo->findByUsername($name);
    }

    public function findAll(): array
    {
        return $this->courseRepo->findAll();
    }

    public function update($id, array $data): ?User
    {
        return $this->update($id, $data);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getOwnCourses($id): Collection
    {
        return $this->findById($id)->getOwnCourses();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getFavCourses($id): Collection
    {
        return $this->findById($id)->getFavoriteCourses();
    }
}