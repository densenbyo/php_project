<?php

namespace App\Repository;


use App\Entity\Lecture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;


class LectureRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lecture::class);
    }

    public function create($name, $content): Lecture
    {
        $em = $this->getEntityManager();
        $lecture = new Lecture($name, $content);
        $em -> persist($lecture);
        $em -> flush();
        return $lecture;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findById($id): ?Lecture
    {
        $qb = $this->getEntityManager()->createQueryBuilder('l')
            ->select('l')
            ->where('l.id = :id')
            ->from('App:Lecture', 'l')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    public function delete(Lecture $user): void
    {
        $em = $this->getEntityManager();
        $em -> remove($user);
        $em -> flush();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findLectureByName($name): ?Lecture
    {
        $qb = $this->getEntityManager()->createQueryBuilder('l')
            ->select('l')
            ->where('l.name = :id')
            ->from('App:Lecture', 'l')
            ->setParameter('id', $name);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    public function findAll(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder('c')->select('c')
            ->from('App:Lecture','c');
        return $qb->getQuery()->getArrayResult();
    }
}