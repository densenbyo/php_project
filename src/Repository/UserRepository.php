<?php

namespace App\Repository;


use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;


class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

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
     */
    public function findById($id): ?User
    {
        $qb = $this->getEntityManager()->createQueryBuilder('u')
            ->select('u')
            ->where('u.id = :id')
            ->from('App:User', 'u')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByUsername($username): ?User
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.username = :id')
            ->from('App:User', 'u')
            ->setParameter('id', $username);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
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
            ->from('App:User', 'u')
            ->setParameter('id', $email);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    public function findAll(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder('u')
            ->select('u')
            ->from('App:User', 'u');
        $query = $qb->getQuery();
        return $query->getArrayResult();
    }

    /**
     * @throws NonUniqueResultException
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