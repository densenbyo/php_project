<?php

namespace repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\User;
use PDO;

require_once 'src/util/config.php';

class UserRepository extends EntityRepository
{
    public function create($username, $email, $password): User
    {
        $em = $this->getEntityManager();
        $hashed_password = password_hash($password, "PHP_IS_SUPER_GOOD");
        $user = new User($username, $email, $hashed_password);
        $em -> persist($user);
        $em -> flush();
        return $user;
    }

    public function delete(User $user): void
    {
        $em = $this->getEntityManager();
        $em -> remove($user);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findById($id): ?User
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.id = :id')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByUsername($username): ?User
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.username = :id')
            ->setParameter('id', $username);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByEmail($email): ?User
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.email = :id')
            ->setParameter('id', $email);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    public function findAll(): array
    {
        $qb = $this->createQueryBuilder('u');
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function update($id, array $data): ?User
    {
        $em = $this->getEntityManager();
        $user =  $this->findById($id);
        if (isset($data['reviews'])) {
            $user -> setReviews($data['reviews']);
        }
        if (isset($data['comments'])) {
            $user -> setComments($data['comments']);
        }
        if (isset($data['ownCourses'])) {
            $user -> setOwnCourses($data['ownCourses']);
        }
        if (isset($data['favCourses'])) {
            $user -> setFavouriteCourses($data['favCourses']);
        }

        $em -> persist($user);
        $em -> flush();
        return $user;
    }
}