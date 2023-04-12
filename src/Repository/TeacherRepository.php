<?php

namespace App\Repository;


use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;


class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

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
     */
    public function findById($id): ?Teacher
    {
        $qb = $this->getEntityManager()->createQueryBuilder('t')
            ->select('t')
            ->where('t.id = :id')
            ->from('App:Teacher', 't')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByUsername($username): ?Teacher
    {
        $qb = $this->getEntityManager()->createQueryBuilder('t')
            ->select('t')
            ->where('t.username = :id')
            ->from('App:Teacher', 't')
            ->setParameter('id', $username);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByEmail($email): ?Teacher
    {
        $qb = $this->getEntityManager()->createQueryBuilder('t')
            ->select('t')
            ->where('t.email = :id')
            ->from('App:Teacher', 't')
            ->setParameter('id', $email);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    public function findAll(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder('t')
            ->select('t')
            ->from('App:Teacher', 't');
        $query = $qb->getQuery();
        return $query->getArrayResult();
    }

    /**
     * @throws NonUniqueResultException
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