<?php

namespace repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\Teacher;
use PDO;

require_once 'src/util/config.php';

class TeacherRepository extends EntityRepository
{
    public function create($username, $email, $password): Teacher
    {
        $em = $this->getEntityManager();
        $hashed_password = password_hash($password, "PHP_IS_SUPER_GOOD");
        $teacher = new Teacher($username, $email, $hashed_password);
        $em -> persist($teacher);
        $em -> flush();
        return $teacher;
    }

    public function delete(Teacher $user): void
    {
        $em = $this->getEntityManager();
        $em -> remove($user);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findById($id): ?Teacher
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.id = :id')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByUsername($username): ?Teacher
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.username = :id')
            ->setParameter('id', $username);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByEmail($email): ?Teacher
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.email = :id')
            ->setParameter('id', $email);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    public function findAll(): array
    {
        $qb = $this->createQueryBuilder('t');
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function update($id, Collection $ownC): ?Teacher
    {
        $em = $this->getEntityManager();
        $teacher =  $this->findById($id);
        $teacher -> setCourses($ownC);
        $em -> persist($teacher);
        $em -> flush();
        return $teacher;
    }
}