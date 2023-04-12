<?php

namespace App\Repository;


use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function create($rating, $message): Review
    {
        $em = $this->getEntityManager();
        $review = new Review($rating, $message);
        $em -> persist($review);
        $em -> flush();
        return $review;
    }

    public function delete(Review $user): void
    {
        $em = $this->getEntityManager();
        $em -> remove($user);
        $em -> flush();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findById($id): ?Review
    {
        $qb = $this->getEntityManager()->createQueryBuilder('r')
            ->select('r')
            ->where('r.id = :id')
            ->from('App:Review', 'r')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }
}