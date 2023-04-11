<?php

namespace repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\Review;

require_once 'src/util/config.php';
class ReviewRepository extends EntityRepository
{
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
     * @throws NoResultException
     */
    public function findById($id): ?Review
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r')
            ->where('r.id = :id')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }
}