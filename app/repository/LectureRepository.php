<?php

namespace repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\Lecture;

require_once 'src/util/config.php';

class LectureRepository extends EntityRepository
{

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
     * @throws NoResultException
     */
    public function findById($id): ?Lecture
    {
        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->where('l.id = :id')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    public function delete(Lecture $user): void
    {
        $em = $this->getEntityManager();
        $em -> remove($user);
        $em -> flush();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findLectureByName($name): ?Lecture
    {
        $qb = $this->createQueryBuilder('l')
            ->select('c')
            ->where('c.name = :name')
            ->setParameter('name', $name);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    public function findAll(): array
    {
        $qb = $this->createQueryBuilder('l');
        $query = $qb->getQuery();
        return $query->getArrayResult();
    }
}