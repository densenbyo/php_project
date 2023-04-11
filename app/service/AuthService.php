<?php

namespace service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\User;
use repository\UserRepository;
use model\Teacher;
use repository\TeacherRepository;

require_once 'src/repository/UserRepository.php';
require_once 'src/repository/TeacherRepository.php';
class AuthService
{
    private $userRepo;
    private $teacherRepo;

    public function __construct(UserRepository $userRepo, TeacherRepository $teacherRepo)
    {
        $this->userRepo = $userRepo;
        $this->teacherRepo = $teacherRepo;
    }

    public function userLogin($username, $password): bool
    {
        try {
            $user = $this->userRepo->findByUsername($username);
            $hashed_password = password_hash($password, "PHP_IS_SUPER_GOOD");
            return password_verify($hashed_password, $user->getPassword());
        } catch (NoResultException|NonUniqueResultException $e) {
            return false;
        }
    }

    public function userRegistration($username, $email, $password): ?User
    {
        try {
            $user = $this->userRepo->findByUsername($username);
            return null;
        } catch (NoResultException|NonUniqueResultException $e) {
            try {
                $user = $this->userRepo->findByEmail($email);
                return null;
            } catch (NoResultException|NonUniqueResultException $e) {
                return $this->userRepo->create($username, $email, $password);
            }
        }
    }

    public function teacherLogin($username, $password): bool
    {
        try {
            $teacher = $this->teacherRepo->findByUsername($username);
            $hashed_password = password_hash($password, "PHP_IS_SUPER_GOOD");
            return password_verify($hashed_password, $teacher->getPassword());
        } catch (NoResultException|NonUniqueResultException $e) {
            return false;
        }
    }

    public function teacherRegistration($username, $email, $password): ?Teacher
    {
        try {
            $teacher = $this->teacherRepo->findByUsername($username);
            return null;
        } catch (NoResultException|NonUniqueResultException $e) {
            try {
                $teacher = $this->teacherRepo->findByEmail($username);
                return null;
            } catch (NoResultException|NonUniqueResultException $e) {
                return $this->teacherRepo->create($username, $email, $password);
            }
        }
    }
}